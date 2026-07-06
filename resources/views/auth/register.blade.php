{{--
    Halaman register akun mahasiswa baru.
--}}
<!DOCTYPE html>
<html lang="id" class="auth-page-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>Daftar ScholarGate</title>
    <link rel="stylesheet" href="{{ asset('css/scholargate.css') }}?v={{ filemtime(public_path('css/scholargate.css')) }}">
    <script>
        // Prevent FOUC
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
</head>
<body class="auth-page auth-page--register auth-page--landing-match">
    <main class="auth-layout">
        <header class="landing-navbar landing-navbar--clean auth-navbar" aria-label="Navigasi autentikasi">
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

            <div class="landing-nav-actions auth-nav-actions">
                <button id="darkModeToggle" class="theme-toggle" aria-label="Toggle Dark Mode">
                    <svg class="sun-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    <svg class="moon-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" style="display: none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <a class="landing-btn landing-btn--ghost" href="{{ route('landing') }}">Beranda</a>
                <a class="landing-btn landing-btn--dark" href="{{ route('login') }}">Masuk</a>
            </div>
        </header>
        <section class="auth-hero-card auth-hero-card--register" aria-labelledby="auth-title">
            <div class="auth-copy auth-copy--register">
                <span class="landing-eyebrow">Portal Beasiswa Modern</span>
                <h1 id="auth-title">Daftar akun untuk mulai pengajuan beasiswa.</h1>
                <p>
                    Lengkapi data mahasiswa satu kali, lalu gunakan ScholarGate untuk memilih beasiswa,
                    mengunggah dokumen persyaratan, dan memantau proses verifikasi admin.
                </p>
            </div>

            <div class="auth-form-side auth-form-side--register">
                <div class="auth-card auth-card--register auth-card--landing">
                    <span class="auth-security-badge">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M7.5 10V7.5a4.5 4.5 0 1 1 9 0V10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <rect x="5" y="10" width="14" height="10" rx="2.5" fill="currentColor"/>
                        </svg>
                        Secure Registration
                    </span>

                    <div class="auth-card__header">
                        <h2>Buat akun mahasiswa</h2>
                        <p>Data ini digunakan sebagai identitas awal pada dashboard pengajuan beasiswa.</p>
                    </div>

                    @if($errors->any())
                        <div class="auth-alert auth-alert--danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.store') }}" class="auth-form auth-form--register">
                        @csrf

                        <div class="auth-register-grid">
                            <div class="auth-field">
                                <label for="name">Nama Lengkap</label>
                                <div class="auth-input-wrap">
                                    <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M4.5 20c.8-4 3.2-6 7.5-6s6.7 2 7.5 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="masukkan nama lengkap" autocomplete="name" pattern="[a-zA-Z\s.,'\-]+" title="Nama lengkap hanya boleh berisi huruf, spasi, titik, koma, tanda petik, dan hubung." required>
                                </div>
                            </div>

                            <div class="auth-field">
                                <label for="nim">NIM</label>
                                <div class="auth-input-wrap">
                                    <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="3.5" y="5" width="17" height="14" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M7 9h6M7 13h10M7 16h7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input id="nim" type="text" name="nim" value="{{ old('nim') }}" placeholder="masukkan NIM" autocomplete="off" pattern="[0-9]+" title="NIM hanya boleh berisi angka." required>
                                </div>
                            </div>

                            <div class="auth-field">
                                <label for="program_studi">Program Studi</label>
                                <div class="auth-input-wrap">
                                    <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m3 9 9-4 9 4-9 4-9-4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M6 11.5V16c2.5 2 9.5 2 12 0v-4.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <select id="program_studi" name="program_studi" required style="padding-right: 20px;">
                                        <option value="">Pilih Program Studi</option>
                                        <option value="Teknik Informatika" {{ old('program_studi') === 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                                        <option value="Sistem Informasi" {{ old('program_studi') === 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                                    </select>
                                </div>
                            </div>

                            <div class="auth-field">
                                <label for="kelas">Kelas</label>
                                <div class="auth-input-wrap">
                                    <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="4" y="4" width="16" height="16" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M8 9h8M8 13h8M8 17h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <x-kelas-selector required="true" is-auth="true" />
                                </div>
                            </div>

                            <div class="auth-field">
                                <label for="phone">No. HP</label>
                                <div class="auth-input-wrap">
                                    <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M7.2 3.5 10 8.2 7.8 10c1.2 2.8 3.4 5 6.2 6.2l1.8-2.2 4.7 2.8c.3.2.5.6.4 1-.5 2-2.2 3.3-4.2 3.2C9.2 20.5 3.5 14.8 3 7.3c-.1-2 1.2-3.7 3.2-4.2.4-.1.8.1 1 .4Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="masukkan nomor HP (10-15 digit angka)" autocomplete="tel" pattern="[0-9]{10,15}" title="Nomor HP harus berupa 10 hingga 15 digit angka saja.">
                                </div>
                            </div>

                            <div class="auth-field">
                                <label for="email">Email</label>
                                <div class="auth-input-wrap">
                                    <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="3" y="5" width="18" height="14" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="m5 8 7 5 7-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="masukkan email anda" autocomplete="email" required>
                                </div>
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="password">Password</label>
                            <div class="auth-input-wrap">
                                <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="5" y="10" width="14" height="10" rx="2.5" fill="currentColor"/>
                                    <path d="M8 10V7.5a4 4 0 0 1 8 0V10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <input id="password" type="password" name="password" placeholder="minimal 6 karakter" autocomplete="new-password" minlength="6" required>
                                <button class="auth-password-toggle" type="button" aria-label="Tampilkan password" aria-controls="password" data-password-toggle="password">
                                    <svg class="eye-open" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="12" cy="12" r="2.7" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                    <svg class="eye-closed" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m3 3 18 18M10.6 6.2A10.5 10.5 0 0 1 12 6c6 0 9.5 6 9.5 6a15.5 15.5 0 0 1-2.4 3.1M6.2 7.2C3.8 9 2.5 12 2.5 12s3.5 6 9.5 6c1.3 0 2.5-.3 3.5-.7M9.9 9.9a3 3 0 0 0 4.2 4.2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="auth-form__options auth-form__options--register">
                            <span>Sudah punya akun?</span>
                            <a href="{{ route('login') }}">Masuk di sini</a>
                        </div>

                        <button type="submit" class="auth-submit">Daftar</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.dataset.passwordToggle);
                var showPassword = input.type === 'password';

                input.type = showPassword ? 'text' : 'password';
                button.classList.toggle('is-visible', showPassword);
                button.setAttribute('aria-label', showPassword ? 'Sembunyikan password' : 'Tampilkan password');
            });
        });

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
