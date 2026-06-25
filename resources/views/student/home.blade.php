@extends('layouts.app')
@section('content')
<section class="dashboard-hero dashboard-hero--student">
    <div class="dashboard-hero__content">
        <span class="dashboard-eyebrow">Dashboard Mahasiswa</span>
        <h1>Ajukan berkas beasiswa dengan alur yang lebih rapi.</h1>
        <p>Pilih jenis beasiswa, unggah dokumen persyaratan, pantau status pengajuan, dan ikuti catatan revisi dari admin prodi.</p>
        <div class="hero-actions dashboard-hero__actions">
            <a href="{{ route('student.applications.create') }}" class="btn warning">Ajukan Berkas Baru</a>
            <a href="{{ route('student.information') }}" class="btn ghost">Lihat Informasi</a>
        </div>
    </div>

    <div class="dashboard-hero__panel" aria-label="Ringkasan pengajuan mahasiswa">
        <div class="dashboard-mini-card">
            <span>Berkas Aktif</span>
            <strong>{{ $activeMatches }}</strong>
            <small>Jenis pengajuan tersedia</small>
        </div>
        <div class="dashboard-mini-card">
            <span>Sedang Diproses</span>
            <strong>{{ $inReview }}</strong>
            <small>Pengajuan dalam review</small>
        </div>
        <div class="dashboard-mini-card">
            <span>Bookmark</span>
            <strong>{{ $bookmarkedCount }}</strong>
            <small>Pengajuan tersimpan</small>
        </div>
    </div>
</section>

<section class="dashboard-summary-row">
    <article class="dashboard-summary-card">
        <span>Langkah 1</span>
        <strong>Pilih Beasiswa</strong>
        <p>Baca informasi dan pastikan dokumen yang diminta sudah siap.</p>
    </article>
    <article class="dashboard-summary-card">
        <span>Langkah 2</span>
        <strong>Upload Dokumen</strong>
        <p>Kirim file persyaratan sesuai format yang diminta sistem.</p>
    </article>
    <article class="dashboard-summary-card">
        <span>Langkah 3</span>
        <strong>Pantau Status</strong>
        <p>Cek hasil review dan unggah revisi bila ada catatan admin.</p>
    </article>
</section>

<div class="section-heading dashboard-section-heading">
    <div>
        <span class="dashboard-eyebrow dashboard-eyebrow--muted">Daftar Pengajuan</span>
        <h2>Beasiswa yang Tersedia</h2>
        <p>Pilih salah satu jenis beasiswa yang ingin diajukan.</p>
    </div>
</div>

<div class="opportunity-grid opportunity-grid--dashboard">
    @forelse($featuredTypes as $type)
        @php
            $isBookmarked = $bookmarkedIds->contains($type->id);
        @endphp

        <article class="opportunity-card opportunity-card--dashboard">
            <div class="opportunity-image {{ strtolower(str_replace(' ', '-', $type->category)) }}">
                @if($type->image_path)
                    <img src="{{ asset('storage/' . $type->image_path) }}" alt="Foto {{ $type->name }}">
                @endif
                <span>{{ strtoupper($type->category) }}</span>
            </div>
            <div class="opportunity-body">
                <div class="card-title-row">
                    <h3>{{ $type->provider ?: $type->name }}</h3>

                    @if($isBookmarked)
                        <form method="POST" action="{{ route('student.bookmarks.destroy', $type) }}" class="bookmark-inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bookmark-icon-button active" aria-label="Hapus {{ $type->name }} dari bookmark">
                                <svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bookmark-icon lucide-bookmark"><path d="M17 3a2 2 0 0 1 2 2v15a1 1 0 0 1-1.496.868l-4.512-2.578a2 2 0 0 0-1.984 0l-4.512 2.578A1 1 0 0 1 5 20V5a2 2 0 0 1 2-2z"/>
                                </svg>
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('student.bookmarks.store', $type) }}" class="bookmark-inline-form">
                            @csrf
                            <button type="submit" class="bookmark-icon-button" aria-label="Simpan {{ $type->name }} ke bookmark">
                                <svg
                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bookmark-icon lucide-bookmark"><path d="M17 3a2 2 0 0 1 2 2v15a1 1 0 0 1-1.496.868l-4.512-2.578a2 2 0 0 0-1.984 0l-4.512 2.578A1 1 0 0 1 5 20V5a2 2 0 0 1 2-2z"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
                <p>{{ \Illuminate\Support\Str::limit($type->description, 95) }}</p>
                <div class="card-divider"></div>
                <div class="card-meta">
                    <span>BATAS WAKTU</span>
                    <strong>{{ $type->deadline?->format('d M Y') ?? '-' }}</strong>
                </div>
                <div class="card-action-row">
                    <a class="text-link" href="{{ route('student.applications.create', ['type' => $type->id]) }}">Ajukan berkas</a>
                    <a class="text-link muted-link" href="{{ route('student.bookmarks.index') }}">Bookmark</a>
                </div>
            </div>
        </article>
    @empty
        <div class="empty-state">Belum ada jenis pengajuan aktif.</div>
    @endforelse
</div>
@endsection
