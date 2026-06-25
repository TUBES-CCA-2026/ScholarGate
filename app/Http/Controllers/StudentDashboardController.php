<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\DocumentType;
use App\Models\StudentApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Menyediakan halaman utama mahasiswa, profil, informasi, dan analitik ringkas.
 */
class StudentDashboardController extends Controller
{
    /**
     * Menampilkan ringkasan dashboard mahasiswa aktif.
     */
    public function home(Request $request): View
    {
        $user = $request->user();
        $bookmarkedIds = $user->bookmarks()->pluck('document_type_id');

        return view('student.home', [
            'applications' => StudentApplication::whereBelongsTo($user)->latest()->take(4)->get(),
            'featuredTypes' => DocumentType::where('is_active', true)->latest()->take(3)->get(),
            'activeMatches' => DocumentType::where('is_active', true)->count(),
            'bookmarkedCount' => $bookmarkedIds->count(),
            'bookmarkedIds' => $bookmarkedIds,
            'inReview' => StudentApplication::whereBelongsTo($user)
                ->whereIn('status', [
                    StudentApplication::STATUS_SUBMITTED,
                    StudentApplication::STATUS_IN_REVIEW,
                    StudentApplication::STATUS_REVISION,
                ])
                ->count(),
        ]);
    }

    /**
     * Menampilkan profil mahasiswa dan riwayat pengajuan.
     */
    public function profile(Request $request): View
    {
        $applications = StudentApplication::whereBelongsTo($request->user())
            ->with('documentType')
            ->latest()
            ->get();

        return view('student.profile', compact('applications'));
    }

    /**
     * Menampilkan katalog beasiswa aktif, bookmark mahasiswa, dan pengumuman.
     */
    public function information(Request $request): View
    {
        return view('student.information', [
            'documentTypes' => DocumentType::where('is_active', true)
                ->with('requirements')
                ->latest()
                ->get(),
            'bookmarkedIds' => $request->user()->bookmarks()->pluck('document_type_id'),
            'announcements' => Announcement::latest()->take(5)->get(),
        ]);
    }

    /**
     * Menampilkan statistik pengajuan mahasiswa aktif.
     */
    public function analytics(Request $request): View
    {
        return view('student.analytics', [
            'summary' => $this->applicationSummary($request->user()->id),
        ]);
    }

    /**
     * Menampilkan form perubahan profil mahasiswa.
     */
    public function editProfile(Request $request): View
    {
        return view('student.edit-profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui data profil dan foto mahasiswa.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate($this->profileRules($user->id));

        if ($request->hasFile('photo')) {
            $this->deletePublicFile($user->photo_path);
            $validated['photo_path'] = $request->file('photo')->store('profile-photos', 'public');
        }

        unset($validated['photo']);

        $user->update($validated);

        return redirect()
            ->route('student.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Menghitung jumlah pengajuan berdasarkan kelompok status.
     */
    private function applicationSummary(int $userId): array
    {
        $baseQuery = StudentApplication::where('user_id', $userId);

        return [
            'total' => (clone $baseQuery)->count(),
            'submitted' => (clone $baseQuery)->where('status', StudentApplication::STATUS_SUBMITTED)->count(),
            'in_review' => (clone $baseQuery)->where('status', StudentApplication::STATUS_IN_REVIEW)->count(),
            'approved' => (clone $baseQuery)->whereIn('status', [
                StudentApplication::STATUS_APPROVED,
                StudentApplication::STATUS_COMPLETED,
            ])->count(),
        ];
    }

    /**
     * Aturan validasi profil mahasiswa.
     */
    private function profileRules(int $userId): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'nim' => ['nullable', 'string', 'max:50', Rule::unique('users', 'nim')->ignore($userId)],
            'program_studi' => ['nullable', 'string', 'max:255'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Menghapus file dari disk public jika path tersedia.
     */
    private function deletePublicFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
