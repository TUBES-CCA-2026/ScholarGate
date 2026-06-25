{{--
    Halaman login ScholarGate untuk admin dan mahasiswa.
--}}
<!DOCTYPE html>
<html lang="id" class="auth-page-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>Masuk ScholarGate</title>
    <link rel="stylesheet" href="{{ asset('css/scholargate.css') }}?v={{ filemtime(public_path('css/scholargate.css')) }}">
</head>
<body class="auth-page auth-page--login auth-page--landing-match">
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
                <a class="landing-btn landing-btn--dark" href="{{ route('register') }}">Daftar</a>
            </div>
        </header>

        <section class="auth-hero-card auth-hero-card--login" aria-labelledby="auth-title">
            <div class="auth-copy">
                <span class="landing-eyebrow">Portal Beasiswa Modern</span>
                <h1 id="auth-title">Masuk untuk memantau pengajuan beasiswa.</h1>
                <p>
                    Akses dashboard ScholarGate untuk melihat informasi beasiswa, mengirim dokumen,
                    memeriksa status pengajuan, dan membaca catatan revisi dari admin prodi.
                </p>

                <div class="auth-mini-flow" aria-label="Ringkasan alur ScholarGate">
                    <article>
                        <strong>01</strong>
                        <span>Login akun mahasiswa atau admin</span>
                    </article>
                    <article>
                        <strong>02</strong>
                        <span>Buka dashboard sesuai role</span>
                    </article>
                    <article>
                        <strong>03</strong>
                        <span>Pantau progres pengajuan</span>
                    </article>
                </div>
            </div>

            <div class="auth-form-side">
                <div class="auth-card auth-card--login auth-card--landing">
                    <span class="auth-security-badge">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M7.5 10V7.5a4.5 4.5 0 1 1 9 0V10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <rect x="5" y="10" width="14" height="10" rx="2.5" fill="currentColor"/>
                        </svg>
                        Secure Login
                    </span>

                    <div class="auth-card__header">
                        <h2>Masuk ke akun</h2>
                        <p>Gunakan email dan password yang sudah terdaftar untuk membuka dashboard ScholarGate.</p>
                    </div>

                    @if(session('success'))
                        <div class="auth-alert auth-alert--success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="auth-alert auth-alert--danger">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                        @csrf

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

                        <div class="auth-field">
                            <label for="password">Password</label>
                            <div class="auth-input-wrap">
                                <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="5" y="10" width="14" height="10" rx="2.5" fill="currentColor"/>
                                    <path d="M8 10V7.5a4 4 0 0 1 8 0V10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <input id="password" type="password" name="password" placeholder="masukkan password" autocomplete="current-password" required>
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

                        <div class="auth-form__options">
                            <label class="auth-check">
                                <input type="checkbox" name="remember" value="1">
                                <span>Remember me</span>
                            </label>
                            <a href="{{ route('register') }}">Buat akun</a>
                        </div>

                        <button type="submit" class="auth-submit">Masuk</button>
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
