@extends('layouts.app')
@section('content')
<div class="page-head-row">
    <div>
        <h1>Pengumuman</h1>
        <p>Publikasikan informasi penting untuk mahasiswa.</p>
    </div>
</div>

<div class="two-column">
    <div class="panel">
        <h2>Tambah Pengumuman</h2>
        <form method="POST" action="{{ route('admin.announcements.store') }}" class="form-stack">
            @csrf
            <label>Judul</label>
            <input type="text" name="title" required>
            <label>Isi Pengumuman</label>
            <textarea name="body" rows="6" required></textarea>
            <label>Tanggal Publikasi</label>
            <input type="datetime-local" name="published_at">
            <button type="submit" class="btn primary">Publikasikan</button>
        </form>
    </div>
    <div class="panel">
        <h2>Daftar Pengumuman</h2>
        @forelse($announcements as $announcement)
            <div class="announcement-item with-action">
                <div>
                    <h3>{{ $announcement->title }}</h3>
                    <p>{{ $announcement->body }}</p>
                    <small>{{ $announcement->published_at?->format('d M Y H:i') }}</small>
                </div>
                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn danger small" type="submit">Hapus</button>
                </form>
            </div>
        @empty
            <p>Belum ada pengumuman.</p>
        @endforelse
    </div>
</div>
@endsection
