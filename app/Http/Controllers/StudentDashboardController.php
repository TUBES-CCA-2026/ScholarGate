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

class StudentDashboardController extends Controller
{
    public function home(Request $request): View
    {
        $user = $request->user();

        return view('student.home', [
            'applications' => StudentApplication::whereBelongsTo($user)->latest()->take(4)->get(),
            'featuredTypes' => DocumentType::where('is_active', true)->latest()->take(3)->get(),
            'activeMatches' => DocumentType::where('is_active', true)->count(),
            'inReview' => StudentApplication::whereBelongsTo($user)
                ->whereIn('status', ['submitted', 'in_review', 'revision'])
                ->count(),
        ]);
    }

    public function profile(Request $request): View
    {
        $applications = StudentApplication::whereBelongsTo($request->user())
            ->with('documentType')
            ->latest()
            ->get();

        return view('student.profile', compact('applications'));
    }

    public function information(): View
    {
        return view('student.information', [
            'documentTypes' => DocumentType::where('is_active', true)
                ->with('requirements')
                ->latest()
                ->get(),
            'announcements' => Announcement::latest()->take(5)->get(),
        ]);
    }

    public function analytics(Request $request): View
    {
        return view('student.analytics', [
            'summary' => $this->applicationSummary($request->user()->id),
        ]);
    }

    public function editProfile(Request $request): View
    {
        return view('student.edit-profile', [
            'user' => $request->user(),
        ]);
    }

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

    private function applicationSummary(int $userId): array
    {
        $baseQuery = StudentApplication::where('user_id', $userId);

        return [
            'total' => (clone $baseQuery)->count(),
            'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'in_review' => (clone $baseQuery)->where('status', 'in_review')->count(),
            'approved' => (clone $baseQuery)->whereIn('status', ['approved', 'completed'])->count(),
        ];
    }

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

    private function deletePublicFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
