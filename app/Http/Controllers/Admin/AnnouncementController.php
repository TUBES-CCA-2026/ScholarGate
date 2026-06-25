<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Mengelola pengumuman yang tampil pada halaman informasi mahasiswa.
 */
class AnnouncementController extends Controller
{
    /**
     * Menampilkan seluruh pengumuman dari yang terbaru.
     */
    public function index(): View
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::latest()->get(),
        ]);
    }

    /**
     * Menyimpan pengumuman baru.
     *
     * Jika admin tidak mengisi waktu publikasi, sistem memakai waktu saat ini
     * agar pengumuman langsung dianggap aktif secara administratif.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:2000'],
            'published_at' => ['nullable', 'date'],
        ]);

        Announcement::create($validated + ['published_at' => now()]);

        return back()->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    /**
     * Menghapus pengumuman yang dipilih admin.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
