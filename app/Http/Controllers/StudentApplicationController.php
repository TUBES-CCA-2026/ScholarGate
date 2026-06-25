<?php

namespace App\Http\Controllers;

use App\Models\ApplicationDocument;
use App\Models\DocumentType;
use App\Models\Requirement;
use App\Models\StudentApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Mengelola siklus pengajuan berkas mahasiswa.
 *
 * Alur utama: mahasiswa memilih master beasiswa, mengisi tujuan, mengunggah
 * dokumen sesuai requirements, melihat detail pengajuan, dan mengirim revisi
 * jika admin menandai dokumen sebagai invalid atau missing.
 */
class StudentApplicationController extends Controller
{
    /** Ekstensi dokumen yang diizinkan untuk unggahan awal dan revisi. */
    private const DOCUMENT_MIMES = 'pdf,jpg,jpeg,png,doc,docx';

    /** Batas ukuran unggahan per berkas dalam kilobyte. */
    private const MAX_DOCUMENT_SIZE_KB = 4096;

    /**
     * Menampilkan daftar pengajuan milik mahasiswa aktif.
     */
    public function index(Request $request): View
    {
        $applications = StudentApplication::whereBelongsTo($request->user())
            ->with(['documentType', 'documents.requirement'])
            ->latest()
            ->paginate(10);

        return view('student.applications.index', compact('applications'));
    }

    /**
     * Menampilkan form pembuatan pengajuan dengan seluruh master aktif.
     */
    public function create(): View
    {
        $documentTypes = DocumentType::where('is_active', true)
            ->with('requirements')
            ->latest()
            ->get();

        return view('student.applications.create', compact('documentTypes'));
    }

    /**
     * Membuat pengajuan baru beserta seluruh baris detail dokumennya.
     *
     * Transaction dipakai agar header pengajuan dan detail dokumen selalu
     * tersimpan sebagai satu kesatuan data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->storeRules());
        $documentType = DocumentType::with('requirements')->findOrFail($validated['document_type_id']);

        $application = DB::transaction(function () use ($request, $documentType, $validated): StudentApplication {
            $application = StudentApplication::create([
                'user_id' => $request->user()->id,
                'document_type_id' => $documentType->id,
                'application_code' => $this->generateApplicationCode(),
                'purpose' => $validated['purpose'],
                'status' => StudentApplication::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);

            $documentType->requirements->each(function (Requirement $requirement) use ($request, $application): void {
                $this->createApplicationDocument($request, $application, $requirement);
            });

            return $application;
        });

        return redirect()
            ->route('student.applications.show', $application)
            ->with('success', 'Pengajuan berhasil dikirim ke prodi.');
    }

    /**
     * Menampilkan detail pengajuan.
     *
     * Mahasiswa hanya boleh melihat pengajuannya sendiri, sedangkan admin tetap
     * dapat membuka detail jika diarahkan dari area pemeriksaan.
     */
    public function show(Request $request, StudentApplication $studentApplication): View
    {
        $this->authorizeView($request, $studentApplication);

        $studentApplication->load(['documentType', 'documents.requirement', 'user']);

        return view('student.applications.show', compact('studentApplication'));
    }

    /**
     * Mengganti file dokumen yang diminta revisi oleh admin.
     */
    public function reviseDocument(
        Request $request,
        StudentApplication $studentApplication,
        ApplicationDocument $applicationDocument
    ): RedirectResponse {
        $this->authorizeRevision($request, $studentApplication, $applicationDocument);

        $request->validate([
            'revision_file' => [
                'required',
                'file',
                'mimes:' . self::DOCUMENT_MIMES,
                'max:' . self::MAX_DOCUMENT_SIZE_KB,
            ],
        ]);

        $this->replaceDocumentFile($applicationDocument, $request->file('revision_file'));
        $this->resubmitApplicationWhenRevisionIsComplete($studentApplication);

        return back()->with('success', 'Berkas revisi berhasil diunggah.');
    }

    /**
     * Aturan validasi pengajuan baru.
     */
    private function storeRules(): array
    {
        return [
            'document_type_id' => ['required', 'exists:document_types,id'],
            'purpose' => ['required', 'string', 'max:2000'],
            'requirement_files' => ['nullable', 'array'],
            'requirement_files.*' => [
                'nullable',
                'file',
                'mimes:' . self::DOCUMENT_MIMES,
                'max:' . self::MAX_DOCUMENT_SIZE_KB,
            ],
            'manual_checks' => ['nullable', 'array'],
        ];
    }

    /**
     * Membuat kode pengajuan ringkas dan mudah dibaca admin.
     */
    private function generateApplicationCode(): string
    {
        return 'APP-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
    }

    /**
     * Membuat satu baris detail dokumen berdasarkan requirement master.
     */
    private function createApplicationDocument(
        Request $request,
        StudentApplication $application,
        Requirement $requirement
    ): void {
        $uploadedFile = $request->file("requirement_files.{$requirement->id}");
        $isCheckedManual = in_array((string) $requirement->id, $request->input('manual_checks', []), true);

        ApplicationDocument::create([
            'student_application_id' => $application->id,
            'requirement_id' => $requirement->id,
            'file_path' => $uploadedFile?->store('application-documents', 'public'),
            'original_name' => $uploadedFile?->getClientOriginalName(),
            'is_checked_manual' => $isCheckedManual,
            'expired_at' => $this->calculateExpirationDate($requirement),
            'status' => $this->initialDocumentStatus($uploadedFile, $isCheckedManual),
        ]);
    }

    /**
     * Menghitung tanggal kedaluwarsa dokumen jika requirement memiliki masa berlaku.
     */
    private function calculateExpirationDate(Requirement $requirement): ?Carbon
    {
        if (! $requirement->has_expiry || ! $requirement->valid_days) {
            return null;
        }

        return now()->addDays($requirement->valid_days);
    }

    /**
     * Menentukan status awal dokumen berdasarkan keberadaan file atau centang manual.
     */
    private function initialDocumentStatus(?UploadedFile $uploadedFile, bool $isCheckedManual): string
    {
        return ($uploadedFile || $isCheckedManual)
            ? ApplicationDocument::STATUS_SUBMITTED
            : ApplicationDocument::STATUS_MISSING;
    }

    /**
     * Menghapus file lama lalu menyimpan file revisi baru.
     */
    private function replaceDocumentFile(ApplicationDocument $document, UploadedFile $file): void
    {
        $this->deletePublicFile($document->file_path);

        $document->update([
            'file_path' => $file->store('application-documents', 'public'),
            'original_name' => $file->getClientOriginalName(),
            'is_checked_manual' => false,
            'status' => ApplicationDocument::STATUS_SUBMITTED,
            'note' => null,
        ]);
    }

    /**
     * Mengembalikan status pengajuan menjadi submitted ketika seluruh revisi selesai.
     */
    private function resubmitApplicationWhenRevisionIsComplete(StudentApplication $application): void
    {
        $stillNeedRevision = $application->documents()
            ->whereIn('status', [ApplicationDocument::STATUS_INVALID, ApplicationDocument::STATUS_MISSING])
            ->exists();

        if (! $stillNeedRevision) {
            $application->update(['status' => StudentApplication::STATUS_SUBMITTED]);
        }
    }

    /**
     * Otorisasi akses detail pengajuan.
     */
    private function authorizeView(Request $request, StudentApplication $application): void
    {
        abort_unless($application->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
    }

    /**
     * Otorisasi unggahan revisi dokumen.
     */
    private function authorizeRevision(
        Request $request,
        StudentApplication $application,
        ApplicationDocument $document
    ): void {
        abort_unless($application->user_id === $request->user()->id, 403);
        abort_unless($document->student_application_id === $application->id, 404);
        abort_unless(
            $application->status === StudentApplication::STATUS_REVISION
            && in_array($document->status, [ApplicationDocument::STATUS_INVALID, ApplicationDocument::STATUS_MISSING], true),
            403
        );
    }

    /**
     * Menghapus file dari storage public jika path masih ada.
     */
    private function deletePublicFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
