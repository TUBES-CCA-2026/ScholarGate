{{--
    Halaman mahasiswa untuk membaca informasi beasiswa dan menyimpan bookmark.
--}}
@extends('layouts.app')

@section('content')
<div class="page-head-row">
    <div>
        <h1>Informasi</h1>
        <p>Katalog pengajuan, syarat berkas, dan pengumuman dari prodi.</p>
    </div>

    <div class="page-head-actions">
        <a href="{{ route('student.bookmarks.index') }}" class="btn neutral">Lihat Bookmark</a>
        <a href="{{ route('student.applications.create') }}" class="btn primary">Ajukan Berkas</a>
    </div>
</div>

<div class="two-column">
    <div class="panel">
        <div class="bookmark-panel-heading">
            <div>
                <h2>Katalog Pengajuan</h2>
                <p>Simpan informasi pengajuan penting agar lebih mudah ditemukan kembali.</p>
            </div>
        </div>

        @forelse($documentTypes as $type)
            @php
                $isBookmarked = $bookmarkedIds->contains($type->id);
            @endphp

            <div class="list-card information-master-card">
                @if($type->image_path)
                    <img
                        class="information-master-image"
                        src="{{ asset('storage/' . $type->image_path) }}"
                        alt="Foto {{ $type->name }}"
                    >
                @else
                    <div class="information-master-image information-master-placeholder" aria-hidden="true">
                        {{ strtoupper(substr($type->category, 0, 1)) }}
                    </div>
                @endif

                <div class="information-master-main">
                    <span class="tag">{{ $type->category }}</span>
                    <h3>{{ $type->name }}</h3>
                    <p>{{ $type->description }}</p>
                    <small>Batas waktu: {{ $type->deadline?->format('d M Y') ?? '-' }}</small>
                </div>

                <div class="information-master-actions">
                    @if($isBookmarked)
                        <form method="POST" action="{{ route('student.bookmarks.destroy', $type) }}">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="bookmark-button active"
                                aria-label="Hapus {{ $type->name }} dari bookmark"
                            >
                                <span>♥</span>
                                Tersimpan
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('student.bookmarks.store', $type) }}">
                            @csrf

                            <button
                                type="submit"
                                class="bookmark-button"
                                aria-label="Simpan {{ $type->name }} ke bookmark"
                            >
                                <span>♡</span>
                                Simpan
                            </button>
                        </form>
                    @endif

                    <a
                        class="btn small information-master-button"
                        href="{{ route('student.applications.create', ['type' => $type->id]) }}"
                    >
                        Pilih
                    </a>
                </div>

                <div class="requirement-list">
                    <strong>Syarat:</strong>

                    <ul>
                        @forelse($type->requirements as $requirement)
                            <li>{{ $requirement->name }}</li>
                        @empty
                            <li>Belum ada syarat dokumen yang ditetapkan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @empty
            <div class="empty-state">
                Belum ada informasi pengajuan aktif.
            </div>
        @endforelse
    </div>

    <div class="panel">
        <h2>Pengumuman</h2>

        @forelse($announcements as $announcement)
            <div class="announcement-item">
                <h3>{{ $announcement->title }}</h3>
                <p>{{ $announcement->body }}</p>
                <small>{{ $announcement->published_at?->format('d M Y H:i') }}</small>
            </div>
        @empty
            <p>Belum ada pengumuman.</p>
        @endforelse
    </div>
</div>
@endsection
