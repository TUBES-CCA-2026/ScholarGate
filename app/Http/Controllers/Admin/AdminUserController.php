<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->latest()
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('nim', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->input('role'));
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s.,\'-]+$/'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_STUDENT])],
            'nim' => ['nullable', 'string', 'max:50', 'regex:/^[0-9]+$/', Rule::unique('users', 'nim')->ignore($user->id)],
            'program_studi' => ['nullable', 'string', 'max:255'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:10', 'max:15'],
        ], [
            'name.regex' => 'Nama lengkap hanya boleh berisi huruf, spasi, dan tanda baca standar.',
            'nim.regex' => 'NIM hanya boleh berisi angka.',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'phone.min' => 'Nomor HP minimal 10 digit.',
            'phone.max' => 'Nomor HP maksimal 15 digit.',
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun user berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['error' => 'Anda tidak bisa menghapus akun Anda sendiri yang sedang aktif.']);
        }

        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }

        // Delete user applications and application documents first to avoid constraint errors
        $user->applications()->each(function($app) {
            foreach ($app->documents as $doc) {
                if ($doc->file_path) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }
            $app->forceDelete();
        });

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun user berhasil dihapus.');
    }
}
