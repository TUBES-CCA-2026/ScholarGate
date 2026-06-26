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
     * Memperbarui status utama pengajuan dan catatan admin.
     */
    public function updateStatus(Request $request, StudentApplication $studentApplication): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . self::APPLICATION_STATUSES],
        ]);

        $studentApplication->update($validated);

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    /**
     * Memperbarui status satu dokumen pada pengajuan tertentu.
     *
     * Guard clause memastikan dokumen yang dikirim pada URL benar-benar milik
     * pengajuan yang sedang dibuka sehingga route model binding tidak dapat
     * dipakai untuk mengubah dokumen milik pengajuan lain.
     */
    public function updateDocument(
        Request $request,
        StudentApplication $studentApplication,
        ApplicationDocument $applicationDocument
    ): RedirectResponse {
        abort_unless($applicationDocument->student_application_id === $studentApplication->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:' . self::DOCUMENT_STATUSES],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $applicationDocument->update($validated);
        $this->markApplicationAsRevisionWhenDocumentIsInvalid($studentApplication, $validated);

        return back()->with('success', 'Status dokumen berhasil diperbarui.');
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
     * Mengubah status pengajuan menjadi revisi ketika minimal satu dokumen tidak valid.
     */
    private function markApplicationAsRevisionWhenDocumentIsInvalid(StudentApplication $application, array $validated): void
    {
        if ($validated['status'] !== ApplicationDocument::STATUS_INVALID) {
            return;
        }

        $application->update([
            'status' => StudentApplication::STATUS_REVISION,
            'admin_note' => $validated['note'] ?: 'Terdapat berkas yang perlu direvisi.',
        ]);
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
    public function restore(int $applicationId): RedirectResponse
    {
        $application = StudentApplication::onlyTrashed()->findOrFail($applicationId);

        $application->restore();

        return redirect()
            ->route('admin.applications.recycle-bin')
            ->with('success', 'Pengajuan berhasil dipulihkan.');
    }

    /**
     * Menghapus pengajuan secara permanen dari recycle bin.
     */
    public function forceDelete(int $applicationId): RedirectResponse
    {
        $application = StudentApplication::onlyTrashed()
            ->with('documents')
            ->findOrFail($applicationId);

        foreach ($application->documents as $document) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
        }

        $application->forceDelete();

        return redirect()
            ->route('admin.applications.recycle-bin')
            ->with('success', 'Pengajuan berhasil dihapus permanen.');
    }
}
