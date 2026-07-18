{{--
    Halaman mahasiswa untuk melihat informasi beasiswa yang sudah disimpan.
--}}
@extends('layouts.app')

@section('content')

<div class="page-head-row">
    <div>
        <h1>Favorite</h1>
        <p>Daftar pengajuan yang Anda simpan untuk dipantau dan diajukan kembali dengan lebih cepat.</p>
    </div>
</div>

<div class="panel bookmark-list-panel">
    <div class="bookmark-panel-heading">
        <div>
            <h2>Pengajuan Tersimpan</h2>
            <p>Gunakan halaman ini sebagai daftar prioritas sebelum mengirim pengajuan berkas.</p>
        </div>
        <span class="bookmark-count-badge">{{ $bookmarks->count() }} tersimpan</span>
    </div>

    @forelse($bookmarks as $bookmark)
        @php
            $type = $bookmark->documentType;
            $daysLeft = $type->deadline ? now()->startOfDay()->diffInDays($type->deadline, false) : null;
        @endphp

        <div class="list-card information-master-card bookmark-master-card">
            @if($type->image_path)
                <img class="information-master-image" src="{{ asset('storage/' . $type->image_path) }}" alt="Foto {{ $type->name }}">
            @else
                <div class="information-master-image information-master-placeholder" aria-hidden="true">{{ strtoupper(substr($type->category, 0, 1)) }}</div>
            @endif

            <div class="information-master-main">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span class="tag">{{ $type->category }}</span>
                    @if($daysLeft !== null)
                        @if($daysLeft < 0)
                            <span class="deadline-countdown expired">⏱️ Expired</span>
                        @elseif($daysLeft <= 3)
                            <span class="deadline-countdown urgent">⏱️ {{ $daysLeft }} hari lagi</span>
                        @elseif($daysLeft <= 7)
                            <span class="deadline-countdown warning">⏱️ {{ $daysLeft }} hari lagi</span>
                        @else
                            <span class="deadline-countdown">⏱️ {{ $daysLeft }} hari lagi</span>
                        @endif
                    @endif
                </div>
                <h3>{{ $type->name }}</h3>
                <p>{{ $type->description }}</p>
                <div class="bookmark-meta-row">
                    <small>Batas waktu: {{ $type->deadline?->format('d M Y') ?? '-' }}</small>
                    <small>Disimpan: {{ $bookmark->created_at?->format('d M Y H:i') }}</small>
                </div>
            </div>

            <div class="information-master-actions">
                <a class="btn small information-master-button" href="{{ route('student.applications.create', ['type' => $type->uid]) }}">Ajukan</a>
                <form method="POST" action="{{ route('student.bookmarks.destroy', $type) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bookmark-button active" aria-label="Hapus {{ $type->name }} dari bookmark">
                        <span>♥</span>
                        Hapus
                    </button>
                </form>
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
        <div class="empty-state bookmark-empty-state" style="border: none; padding: 48px 12px;">
            <div class="empty-state-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </div>
            <strong>Belum Ada Favorite</strong>
            <p>Simpan jenis pengajuan dari menu Informasi agar daftar prioritas Anda muncul di halaman ini.</p>
            <a href="{{ route('student.information') }}" class="btn primary small" style="margin-top: 8px;">Buka Informasi</a>
        </div>
    @endforelse
</div>
@endsection
