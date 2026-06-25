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
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="masukkan nama lengkap" autocomplete="name" required>
                                </div>
                            </div>

                            <div class="auth-field">
                                <label for="nim">NIM</label>
                                <div class="auth-input-wrap">
                                    <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="3.5" y="5" width="17" height="14" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M7 9h6M7 13h10M7 16h7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input id="nim" type="text" name="nim" value="{{ old('nim') }}" placeholder="masukkan NIM" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="auth-field">
                                <label for="program_studi">Program Studi</label>
                                <div class="auth-input-wrap">
                                    <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m3 9 9-4 9 4-9 4-9-4Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="M6 11.5V16c2.5 2 9.5 2 12 0v-4.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input id="program_studi" type="text" name="program_studi" value="{{ old('program_studi') }}" placeholder="masukkan program studi" required>
                                </div>
                            </div>

                            <div class="auth-field">
                                <label for="kelas">Kelas</label>
                                <div class="auth-input-wrap">
                                    <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="4" y="4" width="16" height="16" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M8 9h8M8 13h8M8 17h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input id="kelas" type="text" name="kelas" value="{{ old('kelas') }}" placeholder="masukkan kelas" required>
                                </div>
                            </div>

                            <div class="auth-field">
                                <label for="phone">No. HP</label>
                                <div class="auth-input-wrap">
                                    <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M7.2 3.5 10 8.2 7.8 10c1.2 2.8 3.4 5 6.2 6.2l1.8-2.2 4.7 2.8c.3.2.5.6.4 1-.5 2-2.2 3.3-4.2 3.2C9.2 20.5 3.5 14.8 3 7.3c-.1-2 1.2-3.7 3.2-4.2.4-.1.8.1 1 .4Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="masukkan nomor HP" autocomplete="tel">
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
    </script>
</body>
</html>
