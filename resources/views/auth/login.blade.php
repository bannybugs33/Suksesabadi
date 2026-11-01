<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - BukuTempo Toko Sukses Abadi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Font dimuat tanpa memblokir render: jika Google Fonts lambat/tidak terjangkau, halaman tetap tampil dengan font sistem. --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"></noscript>
    <style>
        :root {
            --ink: #2a2a5c;
            --ink-strong: #23224f;
            --muted: #6b6b86;
            --accent: #a3153f;
            --field: #eeede9;
            --stage: #f0efed;
            --card: #fdfdfc;
            --danger: #b4233a;
            --success: #1f7a4d;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            min-height: 100%;
        }

        body {
            font-family: 'Manrope', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #ffffff;
            color: var(--ink);
            padding: 12px;
            -webkit-font-smoothing: antialiased;
        }

        /* Panggung besar berlatar abu hangat, membingkai kartu hero dan kartu form */
        .stage {
            min-height: calc(100vh - 24px);
            background: var(--stage);
            border-radius: 40px;
            padding: 16px;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
            gap: 16px;
        }

        /* =========================================
           KARTU HERO (kiri) - hanya identitas, tanpa data toko
           ========================================= */
        .hero {
            position: relative;
            overflow: hidden;
            border-radius: 32px;
            padding: 40px 36px 32px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(150deg, #2d2a62 0%, #4c3a78 42%, #7b4768 72%, #a45450 100%);
            box-shadow: 0 24px 50px -18px rgba(45, 42, 98, 0.55);
        }

        .hero::before,
        .hero::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .hero::before {
            width: 320px;
            height: 320px;
            top: -90px;
            right: -70px;
            background: rgba(255, 255, 255, 0.09);
        }

        .hero::after {
            width: 360px;
            height: 360px;
            left: -150px;
            bottom: -170px;
            background: rgba(35, 34, 79, 0.28);
        }

        .hero > * {
            position: relative;
            z-index: 1;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: #ffffff;
            color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-name {
            display: block;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.2px;
            line-height: 1.15;
        }

        .brand-tagline {
            display: block;
            margin-top: 3px;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1.6px;
            color: rgba(255, 255, 255, 0.72);
        }

        .hero-copy {
            margin: 56px 0 40px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.7px;
            text-transform: uppercase;
            margin-bottom: 26px;
        }

        .pill i {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #ffffff;
        }

        .hero h1 {
            font-size: clamp(30px, 3.4vw, 42px);
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -0.8px;
            margin-bottom: 18px;
        }

        .hero p {
            max-width: 400px;
            font-size: 14.5px;
            line-height: 1.65;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.82);
        }

        .hero-footer {
            font-size: 12px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
        }

        /* =========================================
           KARTU FORM (kanan)
           ========================================= */
        .form-side {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 22px;
            padding: 24px 16px;
        }

        .brand-mobile {
            display: none;
            align-items: center;
            gap: 10px;
            color: var(--ink-strong);
            font-weight: 800;
            font-size: 18px;
        }

        .brand-mobile .brand-mark {
            background: var(--ink-strong);
            color: #ffffff;
        }

        .card {
            width: 100%;
            max-width: 440px;
            background: var(--card);
            border-radius: 30px;
            padding: 40px 38px 34px;
            box-shadow: 0 30px 60px -24px rgba(42, 42, 92, 0.22), 0 2px 6px rgba(42, 42, 92, 0.05);
        }

        .card h2 {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--ink);
            line-height: 1.2;
        }

        .card .lead {
            margin-top: 8px;
            font-size: 13.5px;
            line-height: 1.55;
            font-weight: 500;
            color: var(--muted);
        }

        .alert {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 22px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #fdeef0;
            color: var(--danger);
        }

        .alert svg {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert strong {
            display: block;
            font-size: 13px;
            font-weight: 800;
        }

        .alert span {
            display: block;
            margin-top: 2px;
            font-size: 12.5px;
            font-weight: 500;
            line-height: 1.45;
        }

        .alert.is-success {
            background: #e9f6ef;
            color: var(--success);
        }

        form {
            margin-top: 26px;
        }

        .row {
            margin-bottom: 18px;
        }

        .label-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        label.lbl {
            font-size: 12.5px;
            font-weight: 800;
            color: var(--ink);
        }

        .forgot {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
        }

        .forgot:hover {
            text-decoration: underline;
        }

        .field {
            position: relative;
        }

        .field .ico {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ink);
            display: flex;
            pointer-events: none;
        }

        .field input[type="text"],
        .field input[type="password"] {
            width: 100%;
            height: 50px;
            padding: 0 16px 0 46px;
            border: 1.5px solid transparent;
            border-radius: 16px;
            background: var(--field);
            box-shadow: inset 0 2px 5px rgba(42, 42, 92, 0.07);
            font: inherit;
            font-size: 14.5px;
            font-weight: 600;
            color: var(--ink-strong);
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }

        .field input::placeholder {
            color: #a4a3b3;
            font-weight: 500;
        }

        .field input:focus {
            background: #ffffff;
            border-color: var(--ink);
            box-shadow: 0 0 0 4px rgba(42, 42, 92, 0.1);
        }

        .field input[aria-invalid="true"] {
            border-color: #e08a97;
            background: #fff8f9;
        }

        .field.has-toggle input {
            padding-right: 62px;
        }

        .toggle {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 50%;
            background: #ffffff;
            color: var(--ink);
            box-shadow: 0 3px 8px rgba(42, 42, 92, 0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .toggle:hover {
            background: #f7f6f3;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 2px 0 22px;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            cursor: pointer;
            user-select: none;
        }

        .remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--ink);
            cursor: pointer;
        }

        .submit {
            width: 100%;
            height: 52px;
            border: 0;
            border-radius: 18px;
            background: var(--ink);
            color: #ffffff;
            font: inherit;
            font-size: 14.5px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 14px 26px -10px rgba(42, 42, 92, 0.6);
            transition: background 0.15s ease, transform 0.1s ease;
        }

        .submit:hover {
            background: var(--ink-strong);
        }

        .submit:active {
            transform: scale(0.99);
        }

        :focus-visible {
            outline: 3px solid rgba(42, 42, 92, 0.4);
            outline-offset: 2px;
        }

        .secure-note {
            margin-top: 18px;
            text-align: center;
            font-size: 11.5px;
            font-weight: 500;
            color: var(--muted);
        }

        .help {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--muted);
            text-align: center;
        }

        .help a {
            color: var(--ink);
            font-weight: 700;
            text-decoration: none;
        }

        .help a:hover {
            text-decoration: underline;
        }

        /* =========================================
           RESPONSIF
           ========================================= */
        @media (max-width: 920px) {
            body {
                padding: 0;
            }

            .stage {
                grid-template-columns: 1fr;
                border-radius: 0;
                min-height: 100vh;
                padding: 12px;
            }

            .hero {
                display: none;
            }

            .brand-mobile {
                display: flex;
            }

            .card {
                padding: 32px 24px 28px;
            }
        }
    </style>
</head>
<body>
    <div class="stage">
        <aside class="hero" aria-label="Tentang BukuTempo">
            <div class="brand">
                <div class="brand-mark" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2h12a1 1 0 0 1 1 1v18l-3-2-3 2-3-2-3 2V3a1 1 0 0 1 1-1z"></path>
                        <line x1="9" y1="8" x2="15" y2="8"></line>
                        <line x1="9" y1="12" x2="15" y2="12"></line>
                    </svg>
                </div>
                <div>
                    <span class="brand-name">BukuTempo</span>
                    <span class="brand-tagline">CATAT • INGAT • LUNAS</span>
                </div>
            </div>

            <div class="hero-copy">
                <div class="pill"><i></i>Pencatatan toko yang tenang</div>
                <h1>Utang–piutang rapi, tempo tak lagi terlewat.</h1>
                <p>Simpan kuitansi, pantau pembayaran, dan dapatkan pengingat jatuh tempo dalam satu tempat yang mudah dipahami.</p>
            </div>

            <footer class="hero-footer">&copy; 2026 BukuTempo · Dibuat untuk usaha Indonesia</footer>
        </aside>

        <main class="form-side">
            <div class="brand-mobile">
                <div class="brand-mark" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2h12a1 1 0 0 1 1 1v18l-3-2-3 2-3-2-3 2V3a1 1 0 0 1 1-1z"></path>
                        <line x1="9" y1="8" x2="15" y2="8"></line>
                        <line x1="9" y1="12" x2="15" y2="12"></line>
                    </svg>
                </div>
                BukuTempo
            </div>

            <div class="card">
                <h2>Selamat datang kembali</h2>
                <p class="lead">Masuk sebagai owner untuk mengelola arsip dan pembayaran toko.</p>

                @if ($errors->any())
                    <div class="alert" role="alert" id="login-error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>
                            <strong>Belum bisa masuk</strong>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert is-success" role="status">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <div>
                            <strong>Berhasil keluar</strong>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" novalidate>
                    @csrf

                    <div class="row">
                        <div class="label-line">
                            <label for="username" class="lbl">Username</label>
                        </div>
                        <div class="field">
                            <span class="ico" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="{{ old('username', old('email')) }}"
                                required
                                autofocus
                                autocomplete="username"
                                autocapitalize="none"
                                spellcheck="false"
                                @if ($errors->any()) aria-invalid="true" aria-describedby="login-error" @endif
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="label-line">
                            <label for="password" class="lbl">Password</label>
                            <a href="#lupa-password" class="forgot" onclick="alert('Silakan hubungi bantuan@bukutempo.id untuk mereset kata sandi Anda.'); return false;">Lupa password?</a>
                        </div>
                        <div class="field has-toggle">
                            <span class="ico" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                @if ($errors->any()) aria-invalid="true" aria-describedby="login-error" @endif
                            >
                            <button type="button" class="toggle" id="togglePassword" aria-label="Tampilkan password" aria-pressed="false">
                                <svg id="eyeOn" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="eyeOff" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="remember">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Ingat saya di perangkat ini</span>
                    </label>

                    <button type="submit" class="submit">
                        <span>Masuk ke BukuTempo</span>
                        <span aria-hidden="true">→</span>
                    </button>

                    <p class="secure-note">Dilindungi dengan koneksi aman dan terenkripsi</p>
                </form>
            </div>

            <p class="help">Butuh bantuan? Hubungi <a href="mailto:bantuan@bukutempo.id">bantuan@bukutempo.id</a></p>
        </main>
    </div>

    <script>
        (function () {
            var input = document.getElementById('password');
            var btn = document.getElementById('togglePassword');
            var eyeOn = document.getElementById('eyeOn');
            var eyeOff = document.getElementById('eyeOff');

            btn.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                eyeOn.style.display = show ? 'none' : 'block';
                eyeOff.style.display = show ? 'block' : 'none';
                btn.setAttribute('aria-pressed', show ? 'true' : 'false');
                btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
            });
        })();
    </script>
</body>
</html>
