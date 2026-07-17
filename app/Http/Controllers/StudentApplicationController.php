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
use Symfony\Component\HttpFoundation\StreamedResponse;

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
    public function create(Request $request): View
    {
        $documentTypes = DocumentType::where('is_active', true)
            ->with('requirements')
            ->latest()
            ->get();

        $appliedTypeIds = StudentApplication::appliedDocumentTypeIds($request->user()->id);

        return view('student.applications.create', compact('documentTypes', 'appliedTypeIds'));
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
        $documentType = DocumentType::with('requirements')
            ->where('uid', $validated['document_type_uid'])
            ->firstOrFail();

        // Cegah mahasiswa mengajukan beasiswa yang sama dua kali
        $alreadyApplied = StudentApplication::where('user_id', $request->user()->id)
            ->where('document_type_id', $documentType->id)
            ->exists();

        if ($alreadyApplied) {
            return redirect()
                ->route('student.applications.create')
                ->with('error', 'Anda sudah pernah mengajukan beasiswa "' . $documentType->name . '". Satu mahasiswa hanya dapat mengajukan satu kali untuk setiap jenis beasiswa.')
                ->withInput();
        }

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
     * Menampilkan dokumen pengajuan secara aman.
     *
     * Mahasiswa hanya dapat membuka dokumen yang berasal dari
     * pengajuannya sendiri. Admin tetap dapat mengakses dokumen.
     */
    public function viewDocument(
        Request $request,
        StudentApplication $studentApplication,
        ApplicationDocument $applicationDocument
    ): StreamedResponse {
        /*
        * Pastikan mahasiswa hanya membuka pengajuannya sendiri.
        */
        $this->authorizeView(
            $request,
            $studentApplication
        );

        /*
        * Pastikan dokumen benar-benar milik pengajuan yang terdapat
        * pada URL. Ini mencegah pengguna mengganti ID atau UID dokumen.
        */
        abort_unless(
            (int) $applicationDocument->student_application_id
                === (int) $studentApplication->id,
            404,
            'Dokumen tidak ditemukan.'
        );

        abort_unless(
            filled($applicationDocument->file_path),
            404,
            'File belum tersedia.'
        );

        $disk = Storage::disk('public');
        $filePath = $applicationDocument->file_path;

        /*
        * Pastikan file masih tersedia di storage.
        */
        abort_unless(
            $disk->exists($filePath),
            404,
            'File tidak ditemukan di penyimpanan.'
        );

        /*
        * Gunakan nama asli file, tetapi ambil basename untuk mencegah
        * penggunaan path yang tidak aman.
        */
        $fileName = basename(
            $applicationDocument->original_name
                ?: $filePath
        );

        /*
        * File dikirim melalui Laravel, bukan melalui URL /storage langsung.
        */
        return $disk->response(
            $filePath,
            $fileName,
            [
                'Cache-Control' => 'private, no-store, max-age=0',
                'Pragma' => 'no-cache',
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }

    /**
     * Aturan validasi pengajuan baru.
     */
    private function storeRules(): array
    {
        return [
            'document_type_uid' => [
                'required',
                'uuid',
                'exists:document_types,uid',
            ],
            'purpose' => [
            'required',
            'string',
            'max:2000',
            ],
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
        ApplicationDocument::create([
            'student_application_id' => $application->id,
            'requirement_id' => $requirement->id,
            'file_path' => null,
            'original_name' => null,
            'is_checked_manual' => false,
            'expired_at' => $this->calculateExpirationDate($requirement),
            'status' => ApplicationDocument::STATUS_MISSING,
        ]);
    }

    /**
     * Menghitung tanggal kedaluwarsa dokumen jika requirement memiliki masa berlaku.
     */
    private function calculateExpirationDate(Requirement $requirement): ?Carbon
    {
        if (!$requirement->has_expiry || !$requirement->valid_days) {
            return null;
        }

        return now()->addDays($requirement->valid_days);
    }

    /**
     * Otorisasi akses detail pengajuan.
     */
    private function authorizeView(Request $request, StudentApplication $application): void
    {
        abort_unless($application->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
    }
}
