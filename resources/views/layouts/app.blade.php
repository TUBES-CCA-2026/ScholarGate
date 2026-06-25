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
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span>⌂</span> Dasbor</a>
                <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}"><span>▣</span> Pengajuan</a>
                <a href="{{ route('admin.document-types.index') }}" class="nav-link {{ request()->routeIs('admin.document-types.*') ? 'active' : '' }}"><span>✦</span> Master Berkas</a>
                <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}"><span>☰</span> Pengumuman</a>
            @else
                <a href="{{ route('student.home') }}" class="nav-link {{ request()->routeIs('student.home') ? 'active' : '' }}"><span>⌂</span> Beranda</a>
                <a href="{{ route('student.profile') }}" class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}"><span>♙</span> Profil</a>
                <a href="{{ route('student.applications.index') }}" class="nav-link {{ request()->routeIs('student.applications.*') ? 'active' : '' }}"><span>▣</span> Pengajuan</a>
                <a href="{{ route('student.information') }}" class="nav-link {{ request()->routeIs('student.information') ? 'active' : '' }}"><span>☆</span> Informasi</a>
                <a href="{{ route('student.bookmarks.index') }}" class="nav-link {{ request()->routeIs('student.bookmarks.*') ? 'active' : '' }}"><span>♡</span> Bookmark</a>
                <a href="{{ route('student.analytics') }}" class="nav-link {{ request()->routeIs('student.analytics') ? 'active' : '' }}"><span>▥</span> Analitik</a>
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
</body>
</html>
