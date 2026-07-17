<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use App\Models\StudentApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Mengelola proses review pengajuan mahasiswa pada sisi admin.
 *
 * Controller ini bertanggung jawab terhadap daftar pengajuan, detail pengajuan,
 * perubahan status pengajuan, dan validasi status setiap dokumen. Seluruh route
 * yang menggunakan controller ini wajib berada di balik middleware admin.
 */
class AdminApplicationController extends Controller
{
    /** Daftar status utama pengajuan yang diizinkan oleh form admin. */
    private const APPLICATION_STATUSES = 'submitted,in_review,revision,approved,rejected,completed,ready_pickup';

    /** Daftar status dokumen yang diizinkan ketika admin memeriksa lampiran. */
    private const DOCUMENT_STATUSES = 'missing,submitted,valid,invalid,ready';

    /**
     * Menampilkan daftar pengajuan dengan filter status dan kata kunci mahasiswa.
     */
    public function index(Request $request): View
    {
        $applications = StudentApplication::with(['user', 'documentType'])
            ->latest()
            ->when($request->filled('status'), fn (Builder $query): Builder => $query->where('status', $request->input('status')))
            ->when($request->filled('q'), fn (Builder $query): Builder => $this->applyKeywordFilter($query, (string) $request->input('q')))
            ->paginate(12)
            ->withQueryString();

        return view('admin.applications.index', compact('applications'));
    }

    /**
     * Menampilkan detail pengajuan beserta data mahasiswa, master beasiswa, dan dokumen.
     */
    public function show(StudentApplication $studentApplication): View
    {
        $studentApplication->load(['user', 'documentType', 'documents.requirement']);

        return view('admin.applications.show', compact('studentApplication'));
    }

        /**
     * Menampilkan dokumen pengajuan untuk admin.
     *
     * Route ini sudah berada di balik middleware auth dan admin.
     */
    public function viewDocument(
        StudentApplication $studentApplication,
        ApplicationDocument $applicationDocument
    ): StreamedResponse {
        /*
        * Pastikan dokumen benar-benar berasal dari pengajuan yang dibuka.
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

        abort_unless(
            $disk->exists($filePath),
            404,
            'File tidak ditemukan di penyimpanan.'
        );

        $fileName = basename(
            $applicationDocument->original_name
                ?: $filePath
        );

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
     * Memperbarui status utama pengajuan dan catatan admin.
     *
     * Validasi: status tidak bisa diubah ke 'ready_pickup' jika belum semua
     * dokumen berstatus 'ready' atau 'invalid'.
     */
    public function updateStatus(Request $request, StudentApplication $studentApplication): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . self::APPLICATION_STATUSES],
        ]);

        // Cegah ubah ke "Siap Diambil" jika belum semua dokumen siap/dibatalkan
        if ($validated['status'] === 'ready_pickup') {
            $studentApplication->load('documents');
            $allReady = $studentApplication->documents->every(
                fn (ApplicationDocument $doc) => in_array($doc->status, ['ready', 'invalid'])
            );

            if (! $allReady) {
                return back()->with('error', 'Status tidak bisa diubah ke "Siap Diambil" karena masih ada dokumen yang belum berstatus Siap Diambil atau Dibatalkan.');
            }
        }

        $studentApplication->update($validated);

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    /**
     * Memperbarui seluruh status dokumen dalam satu kali simpan.
     *
     * Setelah semua dokumen diperbarui, status pengajuan otomatis dihitung:
     * - Jika ada min. 1 dokumen 'ready' tapi belum semua → 'in_review'
     * - Jika semua dokumen 'ready' atau 'invalid' → 'ready_pickup'
     * - Jika semua masih 'missing' → tetap 'submitted'
     */
    public function updateAllDocuments(Request $request, StudentApplication $studentApplication): RedirectResponse
    {
        $studentApplication->load('documents');

        $validated = $request->validate([
            'documents' => ['required', 'array'],
            'documents.*.status' => ['required', 'in:missing,ready,invalid'],
            'documents.*.file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:4096'],
        ]);

        foreach ($studentApplication->documents as $document) {
            $docData = $validated['documents'][$document->id] ?? null;
            if (! $docData) {
                continue;
            }

            // Upload file jika ada
            if ($request->hasFile("documents.{$document->id}.file")) {
                if ($document->file_path) {
                    Storage::disk('public')->delete($document->file_path);
                }

                $file = $request->file("documents.{$document->id}.file");
                $document->file_path = $file->store('applications/documents', 'public');
                $document->original_name = $file->getClientOriginalName();

                // Auto-set status ke 'ready' saat file diunggah
                $document->status = 'ready';
            } else {
                $document->status = $docData['status'];
            }

            $document->save();
        }

        // Hitung ulang status pengajuan berdasarkan status dokumen
        $this->recalculateApplicationStatus($studentApplication);

        return back()->with('success', 'Semua dokumen berhasil diperbarui.');
    }

    /**
     * Menghitung ulang status pengajuan berdasarkan status seluruh dokumen.
     */
    private function recalculateApplicationStatus(StudentApplication $application): void
    {
        $application->refresh();
        $documents = $application->documents;

        if ($documents->isEmpty()) {
            return;
        }

        $allReadyOrInvalid = $documents->every(
            fn (ApplicationDocument $doc) => in_array($doc->status, ['ready', 'invalid'])
        );

        $hasAtLeastOneReady = $documents->contains(
            fn (ApplicationDocument $doc) => $doc->status === 'ready'
        );

        $allMissing = $documents->every(
            fn (ApplicationDocument $doc) => $doc->status === 'missing'
        );

        if ($allReadyOrInvalid && $hasAtLeastOneReady) {
            $application->update(['status' => StudentApplication::STATUS_READY]);
        } elseif ($hasAtLeastOneReady) {
            $application->update(['status' => StudentApplication::STATUS_IN_REVIEW]);
        } elseif ($allMissing) {
            // Tetap di status saat ini, tidak mundur ke submitted
        }
    }

    /**
     * Menerapkan pencarian berdasarkan nama atau NIM mahasiswa.
     */
    private function applyKeywordFilter(Builder $query, string $keyword): Builder
    {
        return $query->whereHas('user', function (Builder $userQuery) use ($keyword): void {
            $userQuery->where('name', 'like', "%{$keyword}%")
                ->orWhere('nim', 'like', "%{$keyword}%");
        });
    }

    /**
     * Menampilkan daftar pengajuan yang sudah dihapus sementara.
     */
    public function recycleBin(Request $request): View
    {
        $applications = StudentApplication::onlyTrashed()
            ->with(['user', 'documentType'])
            ->latest('deleted_at')
            ->when($request->filled('q'), fn (Builder $query): Builder => $this->applyKeywordFilter($query, (string) $request->input('q')))
            ->paginate(12)
            ->withQueryString();

        return view('admin.applications.recycle-bin', compact('applications'));
    }

    /**
     * Memindahkan pengajuan aktif ke recycle bin.
     */
    public function destroy(StudentApplication $studentApplication): RedirectResponse
    {
        $studentApplication->delete();

        return redirect()
            ->route('admin.applications.index')
            ->with('success', 'Pengajuan berhasil dipindahkan ke recycle bin.');
    }

    /**
     * Memulihkan pengajuan dari recycle bin.
     */
    public function restore(StudentApplication $studentApplication): RedirectResponse 
    {
        abort_unless(
            $studentApplication->trashed(),
            404
        );

        $studentApplication->restore();

        return redirect()
            ->route('admin.applications.recycle-bin')
            ->with(
                'success',
                'Pengajuan berhasil dipulihkan.'
            );
    }

    /**
     * Menghapus pengajuan secara permanen dari recycle bin.
     */
    public function forceDelete(StudentApplication $studentApplication): RedirectResponse 
    {
        abort_unless(
            $studentApplication->trashed(),
            404
        );

        $studentApplication->load('documents');

        foreach ($studentApplication->documents as $document) {
            if ($document->file_path) {
                Storage::disk('public')->delete(
                    $document->file_path
                );
            }
        }

        $studentApplication->forceDelete();

        return redirect()
            ->route('admin.applications.recycle-bin')
            ->with(
                'success',
                'Pengajuan berhasil dihapus permanen.'
            );
    }
}
