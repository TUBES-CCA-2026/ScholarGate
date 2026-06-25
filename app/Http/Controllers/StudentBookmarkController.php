<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\DocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Mengelola bookmark beasiswa pada sisi mahasiswa.
 */
class StudentBookmarkController extends Controller
{
    /**
     * Menampilkan seluruh beasiswa yang disimpan oleh mahasiswa aktif.
     */
    public function index(Request $request): View
    {
        $bookmarks = Bookmark::whereBelongsTo($request->user())
            ->with(['documentType.requirements'])
            ->latest()
            ->get();

        return view('student.bookmarks', compact('bookmarks'));
    }

    /**
     * Menambahkan bookmark pada master beasiswa yang masih aktif.
     */
    public function store(Request $request, DocumentType $documentType): RedirectResponse
    {
        abort_unless($documentType->is_active, 404);

        Bookmark::firstOrCreate([
            'user_id' => $request->user()->id,
            'document_type_id' => $documentType->id,
        ]);

        return back()->with('success', 'Pengajuan berhasil ditambahkan ke bookmark.');
    }

    /**
     * Menghapus bookmark milik mahasiswa aktif.
     */
    public function destroy(Request $request, DocumentType $documentType): RedirectResponse
    {
        Bookmark::where('user_id', $request->user()->id)
            ->where('document_type_id', $documentType->id)
            ->delete();

        return back()->with('success', 'Pengajuan berhasil dihapus dari bookmark.');
    }
}
