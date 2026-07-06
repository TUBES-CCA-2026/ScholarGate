{{--
    Layout utama dashboard yang memuat sidebar, header, konten, dan script navigasi.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ScholarGate' }}</title>
    <link rel="stylesheet" href="{{ asset('css/scholargate.css') }}?v={{ filemtime(public_path('css/scholargate.css')) }}">
</head>
<body class="app-layout">
@php
    /*
     * Data role dipakai untuk menentukan menu sidebar dan shortcut profil.
     * Logika otorisasi tetap berada di middleware, bukan hanya di tampilan.
     */
    $user = auth()->user();
    $isAdmin = $user && $user->role === 'admin';
@endphp
<div class="app-shell">
    <aside class="sidebar" id="mainSidebar" aria-label="Navigasi utama">
        <div class="brand">
            <div class="auth-showcase__brand-icon" aria-hidden="true">
                <svg viewBox="0 0 64 64" role="img">
                    <path d="M5 24.3 31.7 12 59 24.3 31.7 37 5 24.3Z" fill="currentColor"/>
                    <path d="M15.5 30.1v12.2c0 5.2 8 9.7 16.4 9.7s16.4-4.5 16.4-9.7V30.1L32 37.7 15.5 30.1Z" fill="currentColor" opacity=".92"/>
                    <path d="M58.7 25.2v15.2" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                    <circle cx="58.7" cy="44.8" r="3.2" fill="currentColor"/>
                </svg>
            </div>
            <div class="brand-copy">
                <strong>ScholarGate</strong>
                <span>Layanan Akademik</span>
            </div>
            <button class="sidebar-close" type="button" aria-label="Tutup menu navigasi" data-sidebar-close>×</button>
        </div>

        {{-- Menu dirender sesuai role pengguna untuk menjaga fokus navigasi. --}}
        <nav class="nav-menu">
            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </span>
                    Beranda
                </a>
                <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.index') || request()->routeIs('admin.applications.show') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </span>
                    Pengajuan
                </a>
                <a href="{{ route('admin.document-types.index') }}" class="nav-link {{ request()->routeIs('admin.document-types.*') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>
                    </span>
                    Beasiswa
                </a>
                <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </span>
                    Pengumuman
                </a>
                <a href="{{ route('admin.applications.recycle-bin') }}" class="nav-link {{ request()->routeIs('admin.applications.recycle-bin') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </span>
                    Arsip Terhapus
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    Kelola Akun
                </a>
            @else
                <a href="{{ route('student.home') }}" class="nav-link {{ request()->routeIs('student.home') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </span>
                    Beranda
                </a>
                <a href="{{ route('student.applications.index') }}" class="nav-link {{ request()->routeIs('student.applications.*') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </span>
                    Pengajuan
                </a>
                <a href="{{ route('student.information') }}" class="nav-link {{ request()->routeIs('student.information') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </span>
                    Informasi
                </a>
                <a href="{{ route('student.bookmarks.index') }}" class="nav-link {{ request()->routeIs('student.bookmarks.*') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </span>
                    Favorite
                </a>
                <a href="{{ route('student.analytics') }}" class="nav-link {{ request()->routeIs('student.analytics') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </span>
                    Analitik
                </a>
                <a href="{{ route('student.profile') }}" class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    Profile
                </a>
            @endif
        </nav>
    </aside>

    <button class="sidebar-overlay" type="button" aria-label="Tutup menu navigasi" data-sidebar-close></button>

    <main class="main-content">
        <header class="topbar">
            <button class="mobile-menu-toggle" type="button" aria-label="Buka menu navigasi" aria-controls="mainSidebar" aria-expanded="false" data-sidebar-toggle>
                <span></span><span></span><span></span>
            </button>
            <span></span>
            @php
                $user = auth()->user();
                $isStudent = $user && $user->role === 'student';
            @endphp

            <div class="top-actions">
                @if($isStudent)
                    <a href="{{ route('student.profile') }}" class="user-chip user-chip-link" aria-label="Buka profil mahasiswa">
                        <div>
                            <strong>{{ $user->name ?? 'Pengguna' }}</strong>
                            <small>{{ $user->program_studi ?? 'Mahasiswa' }}</small>
                        </div>

                        @if($user->photo_path)
                            <img class="avatar-img" src="{{ asset('storage/' . $user->photo_path) }}" alt="Foto Profil">
                        @else
                            <div class="avatar">{{ strtoupper(substr($user->name ?? 'P', 0, 1)) }}</div>
                        @endif
                    </a>
                @else
                    <div class="user-chip">
                        <div>
                            <strong>{{ $user->name ?? 'Admin' }}</strong>
                            <small>Admin Prodi</small>
                        </div>

                        @if($user->photo_path)
                            <img class="avatar-img" src="{{ asset('storage/' . $user->photo_path) }}" alt="Foto Profil">
                        @else
                            <div class="avatar">{{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}</div>
                        @endif
                    </div>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit">Keluar</button>
                </form>
            </div>
        </header>

        <section class="content-area">
            {{-- Flash message global dari controller. --}}
            @if(session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert danger">
                    {{ $errors->first() }}
                </div>
            @endif
            @yield('content')
        </section>

        <footer class="app-footer">
            <div class="footer-content">
                <span>&copy; {{ date('Y') }} ScholarGate. Hak Cipta Dilindungi.</span>
                <span>Layanan Akademik Program Studi</span>
            </div>
        </footer>
    </main>
</div>
<script>
    (() => {
        // Mengatur sidebar mobile tanpa dependency eksternal.
        const body = document.body;
        const toggle = document.querySelector('[data-sidebar-toggle]');
        const closeButtons = document.querySelectorAll('[data-sidebar-close]');
        const sidebarLinks = document.querySelectorAll('#mainSidebar a');
        const desktopBreakpoint = 980;

        if (!toggle) return;

        const setSidebarState = (isOpen) => {
            body.classList.toggle('sidebar-open', isOpen);
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        toggle.addEventListener('click', () => {
            setSidebarState(!body.classList.contains('sidebar-open'));
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', () => setSidebarState(false));
        });

        sidebarLinks.forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= desktopBreakpoint) setSidebarState(false);
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setSidebarState(false);
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > desktopBreakpoint) setSidebarState(false);
        });
    })();
</script>

<!-- Custom Confirm Modal -->
<div id="confirmModal" class="confirm-modal-overlay" style="display: none;">
    <div class="confirm-modal-box">
        <div class="confirm-modal-header">
            <h3>Konfirmasi Tindakan</h3>
        </div>
        <div class="confirm-modal-body">
            <p id="confirmModalMessage">Apakah Anda yakin ingin melakukan tindakan ini?</p>
        </div>
        <div class="confirm-modal-footer">
            <button id="confirmModalCancel" class="btn neutral">Batal</button>
            <button id="confirmModalConfirm" class="btn danger">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<script>
    // Custom Confirm Modal Logic
    (() => {
        const modal = document.getElementById('confirmModal');
        const messageEl = document.getElementById('confirmModalMessage');
        const confirmBtn = document.getElementById('confirmModalConfirm');
        const cancelBtn = document.getElementById('confirmModalCancel');
        if (!modal || !confirmBtn || !cancelBtn) return;
        let formToSubmit = null;

        document.addEventListener('submit', (e) => {
            const form = e.target.closest('form[data-confirm]');
            if (!form) return;
            
            if (form.dataset.confirmed === 'true') {
                return;
            }

            e.preventDefault();
            formToSubmit = form;
            
            messageEl.textContent = form.getAttribute('data-confirm');
            
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        });

        const closeModal = () => {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 200);
            formToSubmit = null;
        };

        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        confirmBtn.addEventListener('click', () => {
            if (formToSubmit) {
                formToSubmit.dataset.confirmed = 'true';
                formToSubmit.submit();
            }
            closeModal();
        });
    })();
</script>
</body>
</html>
