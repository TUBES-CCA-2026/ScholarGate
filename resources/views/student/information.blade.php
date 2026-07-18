{{--
    Halaman mahasiswa untuk membaca informasi beasiswa dan menyimpan bookmark.
--}}
@extends('layouts.app')

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('student.home') }}">Beranda</a>
        <span class="breadcrumb-sep">&gt;</span>
        <span class="breadcrumb-current">Informasi Beasiswa</span>
    </div>

<div class="page-head-row">
    <div>
        <h1>Informasi</h1>
        <p>Katalog pengajuan, syarat berkas, dan pengumuman dari prodi.</p>
    </div>

    <div class="page-head-actions">
        <a href="{{ route('student.bookmarks.index') }}" class="btn neutral">Lihat Favorite</a>
        <a href="{{ route('student.applications.create') }}" class="btn primary">Ajukan Berkas</a>
    </div>
</div>

<div class="two-column">
    <div class="panel">
        <div class="bookmark-panel-heading" style="margin-bottom: 16px;">
            <div>
                <h2>Katalog Pengajuan</h2>
                <p>Simpan informasi pengajuan penting agar lebih mudah ditemukan kembali.</p>
            </div>
        </div>

        {{-- Instant Search & Category Filter --}}
        <div class="search-filter-bar">
            <div class="search-input-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="catalogSearch" placeholder="Cari nama beasiswa...">
            </div>
            <select id="categoryFilter" class="filter-select">
                <option value="">Semua Kategori</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
                <option value="Prestasi">Prestasi</option>
                <option value="Kepemimpinan">Kepemimpinan</option>
                <option value="Riset">Riset</option>
                <option value="Sosial">Sosial</option>
                <option value="Olahraga">Olahraga</option>
            </select>
        </div>

        <div id="catalogContainer">
            @forelse($documentTypes as $type)
                @php
                    $isBookmarked = $bookmarkedIds->contains($type->id);
                    $isApplied = $appliedTypeIds->contains($type->id);
                    $daysLeft = $type->deadline ? now()->startOfDay()->diffInDays($type->deadline, false) : null;
                @endphp

                <div class="list-card information-master-card catalog-item" id="scholarship-{{ $type->id }}" data-name="{{ strtolower($type->name) }}" data-category="{{ $type->category }}">
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
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span class="tag">{{ $type->category }}</span>
                            @if($daysLeft !== null)
                                @if($daysLeft < 0)
                                    <span class="deadline-countdown expired">Expired</span>
                                @elseif($daysLeft <= 3)
                                    <span class="deadline-countdown urgent">{{ $daysLeft }} hari lagi</span>
                                @elseif($daysLeft <= 7)
                                    <span class="deadline-countdown warning">{{ $daysLeft }} hari lagi</span>
                                @else
                                    <span class="deadline-countdown">{{ $daysLeft }} hari lagi</span>
                                @endif
                            @endif
                            @if($isApplied)
                                <span class="tag" style="background: #dcfce7; color: #166534; font-size: 0.75rem; font-weight: 600;">✓ Sudah Diajukan</span>
                            @endif
                        </div>
                        <h3>{{ $type->name }}</h3>
                        <p>{{ $type->description }}</p>
                        <small>Batas waktu: {{ $type->deadline?->format('d M Y') ?? '-' }}</small>
                    </div>

                    <div class="information-master-actions">
                        @if($isBookmarked)
                            <form method="POST" action="{{ route('student.bookmarks.destroy', $type) }}" class="bookmark-ajax-form">
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
                            <form method="POST" action="{{ route('student.bookmarks.store', $type) }}" class="bookmark-ajax-form">
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

                        @if($isApplied)
                            <span
                                class="btn neutral small information-master-button"
                                style="pointer-events: none; opacity: 0.6;"
                            >
                                Sudah Diajukan
                            </span>
                        @else
                            <a
                                class="btn primary small information-master-button"
                                href="{{ route('student.applications.create', ['type' => $type->id]) }}"
                            >
                                Pilih
                            </a>
                        @endif
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
                    <div class="requirement-list">
                        <a class="text-link master-link" href="{{ $type->registration_link }}" target="_blank" rel="noopener noreferrer">Buka link pendaftaran</a>
                    </div>
                </div>
                
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect>
                            <line x1="7" y1="2" x2="7" y2="22"></line>
                            <line x1="17" y1="2" x2="17" y2="22"></line>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                        </svg>
                    </div>
                    <strong>Belum Ada Informasi Beasiswa</strong>
                    <p>Saat ini belum ada jenis pengajuan beasiswa aktif dari prodi.</p>
                </div>
            @endforelse
        </div>

        {{-- Client-side empty state for search results --}}
        <div id="searchEmptyState" class="empty-state" style="display: none;">
            <div class="empty-state-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <strong>Hasil Pencarian Nihil</strong>
            <p>Tidak ada beasiswa yang cocok dengan kata kunci atau filter kategori Anda.</p>
        </div>
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
            <div class="empty-state" style="border: none; padding: 24px 0;">
                <div class="empty-state-icon" style="width: 48px; height: 48px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>
                <strong>Belum Ada Pengumuman</strong>
            </div>
        @endforelse
    </div>
</div>

<script>
(() => {
    const searchInput = document.getElementById('catalogSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const items = document.querySelectorAll('.catalog-item');
    const emptyState = document.getElementById('searchEmptyState');

    const filterItems = () => {
        const query = searchInput.value.toLowerCase().trim();
        const category = categoryFilter.value;
        let visibleCount = 0;

        items.forEach(item => {
            const matchesQuery = !query || item.dataset.name.includes(query);
            const matchesCategory = !category || item.dataset.category === category;

            if (matchesQuery && matchesCategory) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (visibleCount === 0 && items.length > 0) {
            emptyState.style.display = '';
        } else {
            emptyState.style.display = 'none';
        }
    };

    if (searchInput) searchInput.addEventListener('input', filterItems);
    if (categoryFilter) categoryFilter.addEventListener('change', filterItems);
})();
</script>
@endsection
