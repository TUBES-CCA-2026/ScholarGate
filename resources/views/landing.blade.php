{{--
    Landing page publik yang memperkenalkan fungsi utama ScholarGate.
--}}
<!DOCTYPE html>
<html lang="id" class="landing-page-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>ScholarGate - Portal Beasiswa Akademik</title>
    <link rel="stylesheet" href="{{ asset('css/scholargate.css') }}?v={{ filemtime(public_path('css/scholargate.css')) }}">
    <script>
        // Prevent FOUC
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
</head>
<body class="landing-page landing-page--clean">
    <main class="landing-shell landing-shell--clean">
        <header class="landing-navbar landing-navbar--clean" aria-label="Navigasi utama">
            <a href="{{ route('landing') }}" class="landing-brand" aria-label="ScholarGate beranda">
                <span class="landing-brand__icon" aria-hidden="true">
                    <svg viewBox="0 0 64 64" role="img">
                        <path d="M5 24.3 31.7 12 59 24.3 31.7 37 5 24.3Z" fill="currentColor"/>
                        <path d="M15.5 30.1v12.2c0 5.2 8 9.7 16.4 9.7s16.4-4.5 16.4-9.7V30.1L32 37.7 15.5 30.1Z" fill="currentColor" opacity=".92"/>
                        <path d="M58.7 25.2v15.2" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                        <circle cx="58.7" cy="44.8" r="3.2" fill="currentColor"/>
                    </svg>
                </span>
                <span>
                    <strong>ScholarGate</strong>
                    <small>Portal Beasiswa Akademik</small>
                </span>
            </a>

            <div class="landing-nav-actions">
                <button id="darkModeToggle" class="theme-toggle" aria-label="Toggle Dark Mode">
                    <svg class="sun-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    <svg class="moon-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" style="display: none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                @auth
                    @php
                        $dashboardRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : route('student.home');
                    @endphp
                    <a class="landing-btn landing-btn--dark" href="{{ $dashboardRoute }}">Dashboard</a>
                @else
                    <a class="landing-btn landing-btn--ghost" href="{{ route('login') }}">Masuk</a>
                    <a class="landing-btn landing-btn--dark" href="{{ route('register') }}">Daftar</a>
                @endauth
            </div>
        </header>

        <section class="landing-hero landing-hero--clean" aria-labelledby="landing-title">
            <div class="landing-hero__content">
                <span class="landing-eyebrow">Portal Beasiswa Modern</span>
                <h1 id="landing-title">Kelola pengajuan beasiswa mahasiswa dalam satu sistem.</h1>
                <p>
                    ScholarGate membantu mahasiswa melihat informasi beasiswa, mengunggah dokumen persyaratan,
                    memantau status pengajuan, dan menerima catatan revisi dari admin prodi secara lebih terstruktur.
                </p>

                <div class="landing-hero__actions">
                    @auth
                        @php
                            $dashboardRoute = auth()->user()->role === 'admin' ? route('admin.dashboard') : route('student.home');
                        @endphp
                        <a class="landing-btn landing-btn--primary" href="{{ $dashboardRoute }}">Buka Dashboard</a>
                    @else
                        <a class="landing-btn landing-btn--primary" href="{{ route('register') }}">Mulai Pengajuan</a>
                        <a class="landing-btn landing-btn--light" href="{{ route('login') }}">Masuk Akun</a>
                    @endauth
                </div>
            </div>

            <div class="landing-hero__visual landing-hero__visual--clean" aria-label="Ilustrasi dashboard ScholarGate">
                <article class="landing-dashboard-card landing-dashboard-card--main landing-dashboard-card--clean">
                    <div class="landing-dashboard-card__top">
                        <span></span><span></span><span></span>
                    </div>
                    <div class="landing-dashboard-profile">
                        <div class="landing-avatar-mini" style="background: #04051a; color: #ffd65a; display: flex; align-items: center; justify-content: center;">
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>
                        </div>
                        <div>
                            <strong>Pengajuan Beasiswa</strong>
                            <small>Dokumen sedang diverifikasi admin.</small>
                        </div>
                    </div>
                    <div class="landing-progress-list">
                        <div><span>Profil Mahasiswa</span><strong style="color: #117a4d;">Lengkap</strong></div>
                        <div><span>Upload Pengajuan Berkas</span><strong style="color: #273b88;">Diproses</strong></div>
                        <div><span>Review Admin</span><strong style="color: #8a6200;">Menunggu</strong></div>
                    </div>
                    <div class="landing-progress-bar"><span></span></div>
                </article>
            </div>
        </section>

        <section class="landing-section landing-section--clean" id="fitur">
            <div class="landing-section__head landing-section__head--compact">
                <span class="landing-section-label">Fitur Utama</span>
                <h2>Fitur dibuat sesuai kebutuhan mahasiswa dan admin prodi.</h2>
            </div>

            <div class="landing-feature-grid landing-feature-grid--clean">
                <article class="landing-feature-card">
                    <span>01</span>
                    <h3>Informasi Beasiswa</h3>
                    <p>Mahasiswa dapat membaca informasi, ketentuan, dan dokumen yang perlu disiapkan.</p>
                </article>
                <article class="landing-feature-card">
                    <span>02</span>
                    <h3>Upload Pengajuan Berkas</h3>
                    <p>Pengajuan berkas dikirim oleh sistem langsung ke Prodi.</p>
                </article>
                <article class="landing-feature-card">
                    <span>03</span>
                    <h3>Review Admin</h3>
                    <p>Admin dapat memeriksa, memberi catatan revisi, dan mengubah status pengajuan.</p>
                </article>
            </div>
        </section>

        <section class="landing-flow landing-flow--clean" id="alur">
            <div class="landing-flow__content">
                <span class="landing-section-label">Alur Singkat</span>
                <h2>Dari daftar akun sampai pengajuan selesai.</h2>
            </div>
            <div class="landing-flow__steps landing-flow__steps--clean">
                <article><strong>1</strong><span>Daftar atau masuk akun</span></article>
                <article><strong>2</strong><span>Pilih beasiswa</span></article>
                <article><strong>3</strong><span>Upload pengajuan berkas</span></article>
                <article><strong>4</strong><span>Pantau status</span></article>
            </div>
        </section>

        <footer class="landing-footer-new">
            <div class="landing-footer-grid">
                <div class="landing-footer-col">
                    <h3 class="landing-footer-title landing-footer-title--brand">ScholarGate</h3>
                    <p>Layanan portal pengajuan berkas beasiswa terpadu, membantu mempermudah pendaftaran dan monitoring status berkas mahasiswa.</p>
                </div>
                <div class="landing-footer-col">
                    <h3 class="landing-footer-title">Navigasi</h3>
                    <ul class="landing-footer-links">
                        <li><a href="#fitur">Fitur Utama</a></li>
                        <li><a href="#alur">Alur Pengajuan</a></li>
                        <li><a href="{{ route('login') }}">Masuk Akun</a></li>
                    </ul>
                </div>
                <div class="landing-footer-col">
                    <h3 class="landing-footer-title">Hubungi Kami</h3>
                    <p>
                        Email: info@scholargate.id<br>
                        Telepon: +62 812 4145 6546<br>
                        Gedung Rektorat Lt. 2, Kampus Pusat
                    </p>
                </div>
            </div>
            <div class="landing-footer-bottom">
                <p>&copy; {{ date('Y') }} ScholarGate. Semua hak cipta dilindungi.</p>
            </div>
        </footer>
    </main>
    <script>
        // Dark Mode Logic
        (() => {
            const toggleBtn = document.getElementById('darkModeToggle');
            if (!toggleBtn) return;
            const sunIcon = toggleBtn.querySelector('.sun-icon');
            const moonIcon = toggleBtn.querySelector('.moon-icon');
            
            const updateIcons = (isDark) => {
                if (isDark) {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                } else {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                }
            };

            const currentTheme = document.documentElement.getAttribute('data-theme');
            updateIcons(currentTheme === 'dark');

            toggleBtn.addEventListener('click', () => {
                let targetTheme = 'light';
                if (document.documentElement.getAttribute('data-theme') !== 'dark') {
                    targetTheme = 'dark';
                }
                document.documentElement.setAttribute('data-theme', targetTheme);
                localStorage.setItem('theme', targetTheme);
                updateIcons(targetTheme === 'dark');
            });
        })();
    </script>
</body>
</html>
