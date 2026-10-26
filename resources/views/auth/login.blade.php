<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun - BukuTempo Toko Sukses Abadi</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f7f8fa;
            color: #292930;
            font-family: Arial, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            min-height: 100vh;
        }

        /* ===== Layout dua kolom ===== */
        .login-layout {
            display: grid;
            grid-template-columns: 42fr 58fr;
            min-height: 100vh;
        }

        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
        }

        .hero-panel {
            background: #91283b;
            color: #ffffff;
            padding: 32px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 32px;
        }

        .hero-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hero-brand-badge {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: #c8374b;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-brand-name {
            font-size: 15px;
            font-weight: 600;
            line-height: 1.1;
        }

        .hero-brand-name small {
            display: block;
            margin-top: 3px;
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 1.2px;
            color: rgba(255, 255, 255, 0.75);
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .hero-pill::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #ff8a9b;
        }

        .hero-title {
            font-size: 34px;
            line-height: 1.2;
            font-weight: 600;
            letter-spacing: -0.5px;
            margin: 18px 0 14px;
        }

        .hero-desc {
            font-size: 13px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.8);
            max-width: 340px;
        }

        .preview-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 18px;
        }

        .preview-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .preview-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.72);
        }

        .preview-value {
            font-size: 22px;
            font-weight: 600;
            margin-top: 2px;
        }

        .preview-trend {
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .preview-bars {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            height: 76px;
            margin: 16px 0 14px;
        }

        .preview-bar {
            flex: 1;
            border-radius: 4px 4px 0 0;
            background: rgba(255, 255, 255, 0.22);
        }

        .preview-bar.is-active {
            background: #c8374b;
        }

        .preview-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            background: #ffffff;
            color: #292930;
            border-radius: 10px;
        }

        .preview-alert-icon {
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #fdf0d5;
            color: #b45309;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-alert-icon.is-aman {
            background: #e7f5ec;
            color: #216d44;
        }

        .preview-alert-title {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a24;
        }

        .preview-alert-sub {
            font-size: 10.5px;
            color: #696b78;
        }

        .preview-alert-link {
            margin-left: auto;
            font-size: 11px;
            font-weight: 700;
            color: #91283b;
            text-decoration: none;
        }

        .preview-alert-link:hover {
            text-decoration: underline;
        }

        .hero-footer {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
        }

        @media (max-width: 960px) {
            .login-layout {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .hero-panel {
                padding: 24px;
                gap: 20px;
            }

            .hero-copy,
            .preview-chart,
            .hero-footer {
                display: none;
            }

            .form-panel {
                padding: 24px 16px 32px;
            }
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e6e7ec;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 10px 25px rgba(32, 32, 48, 0.04);
        }

        .card-header {
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a24;
        }

        .card-desc {
            font-size: 13px;
            color: #696b78;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 8px;
            color: #383a45;
        }

        .form-input {
            width: 100%;
            min-height: 44px;
            padding: 10px 14px;
            border: 1px solid #d8dbe2;
            border-radius: 8px;
            background: #ffffff;
            color: #292930;
            font-size: 14px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #91283b;
            box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.15);
        }

        .form-input.is-invalid {
            border-color: #bd443e;
            background-color: #fff9f9;
        }

        .invalid-feedback {
            color: #bd443e;
            font-size: 12px;
            margin-top: 6px;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            color: #555765;
        }

        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #91283b;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            min-height: 46px;
            background: #91283b;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease, transform 0.05s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: #782032;
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .alert-success {
            background: #eef8f2;
            border: 1px solid #c3e6d1;
            color: #216d44;
        }

        .alert-error {
            background: #fdf2f2;
            border: 1px solid #f8d7da;
            color: #98222c;
        }

        .demo-accounts {
            margin-top: 24px;
            padding: 16px;
            background: #fafbfc;
            border: 1px dashed #d8dbe2;
            border-radius: 10px;
        }

        .demo-title {
            font-size: 11px;
            font-weight: 700;
            color: #696b78;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .demo-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .demo-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            padding: 6px 10px;
            background: #ffffff;
            border: 1px solid #eceef2;
            border-radius: 6px;
            cursor: pointer;
            transition: border-color 0.15s ease;
        }

        .demo-item:hover {
            border-color: #91283b;
            background: #fcf5f6;
        }

        .demo-info strong {
            color: #1a1a24;
            display: block;
        }

        .demo-info span {
            color: #717381;
            font-size: 11px;
        }

        .demo-btn-fill {
            font-size: 11px;
            color: #91283b;
            font-weight: 600;
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px 6px;
        }

        .footer-note {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #8b8e9c;
        }
    </style>
</head>

<body>
    <div class="login-layout">
        <aside class="hero-panel" aria-label="Ringkasan BukuTempo">
            <div class="hero-brand">
                <div class="hero-brand-badge">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 4h7a3 3 0 0 1 3 3v13a2 2 0 0 0-2-2H2z"></path>
                        <path d="M22 4h-7a3 3 0 0 0-3 3v13a2 2 0 0 1 2-2h8z"></path>
                    </svg>
                </div>
                <div class="hero-brand-name">
                    BukuTempo
                    <small>CATAT • INGAT • LUNAS</small>
                </div>
            </div>

            <div class="hero-copy">
                <span class="hero-pill">Pencatatan toko yang tenang</span>
                <h1 class="hero-title">Utang–piutang rapi, tempo tak lagi terlewat.</h1>
                <p class="hero-desc">Simpan kuitansi, pantau pembayaran, dan dapatkan pengingat jatuh tempo dalam satu tempat yang mudah dipahami.</p>
            </div>

            @php
            $ringkasRupiah = fn (float $nilai): string => $nilai >= 1000000
            ? 'Rp'.number_format($nilai / 1000000, 2, ',', '.').' jt'
            : 'Rp'.number_format($nilai, 0, ',', '.');
            $perubahan = $grafikPiutang['perubahan'];
            @endphp

            <div class="preview-card">
                <div class="preview-chart">
                    <div class="preview-top">
                        <div>
                            <div class="preview-label">Piutang bulan ini</div>
                            <div class="preview-value">{{ $ringkasRupiah($grafikPiutang['total']) }}</div>
                        </div>
                        @if (! is_null($perubahan))
                        <span class="preview-trend">
                            {{ $perubahan >= 0 ? '↗ +' : '↘ −' }}{{ number_format(abs($perubahan), 1, ',', '.') }}%
                        </span>
                        @endif
                    </div>

                    <div class="preview-bars" aria-hidden="true">
                        @foreach ($grafikPiutang['bars'] as $bar)
                        <div class="preview-bar {{ $bar['aktif'] ? 'is-active' : '' }}" style="height: {{ $bar['tinggi'] }}%;" title="{{ $bar['label'] }}"></div>
                        @endforeach
                    </div>
                </div>

                <div class="preview-alert">
                    @if ($pengingat['jumlah'] > 0)
                    <div class="preview-alert-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="preview-alert-title">{{ $pengingat['jumlah'] }} kuitansi perlu perhatian</div>
                        <div class="preview-alert-sub">Rp{{ number_format($pengingat['sisa'], 0, ',', '.') }} belum diselesaikan</div>
                    </div>
                    <a href="{{ route('jatuh-tempo.index') }}" class="preview-alert-link">Lihat</a>
                    @else
                    <div class="preview-alert-icon is-aman">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <div class="preview-alert-title">Tidak ada kuitansi yang perlu perhatian</div>
                        <div class="preview-alert-sub">Belum ada tagihan yang mendekati jatuh tempo</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="hero-footer">&copy; {{ date('Y') }} BukuTempo • Dibuat untuk usaha Indonesia</div>
        </aside>

        <div class="form-panel">
            <div class="login-container">
                <main class="login-card">
                    <div class="card-header">
                        <h2 class="card-title">Masuk ke Sistem</h2>
                        <p class="card-desc">Gunakan akun Anda untuk mengelola kuitansi dan rekapitulasi.</p>
                    </div>

                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @if ($errors->has('email') && !old('email'))
                    <div class="alert alert-error" role="alert">
                        {{ $errors->first('email') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input @error('email') is-invalid @enderror"
                                value="{{ old('email', 'andi@suksesabadi.com') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="nama@suksesabadi.com">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input @error('password') is-invalid @enderror"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                value="password">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                <span>Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <button type="submit" class="btn-submit">
                            Masuk Sekarang
                        </button>
                    </form>

                    <div class="demo-accounts">
                        <div class="demo-title">Akun Demo Toko Sukses Abadi (Password: password)</div>
                        <div class="demo-list">
                            <div class="demo-item" onclick="fillLogin('andi@suksesabadi.com')">
                                <div class="demo-info">
                                    <strong>Andi Saputra</strong>
                                    <span>andi@suksesabadi.com (Owner)</span>
                                </div>
                                <span class="demo-btn-fill">Pilih</span>
                            </div>
                            <div class="demo-item" onclick="fillLogin('safira@suksesabadi.com')">
                                <div class="demo-info">
                                    <strong>Safira Alifiya</strong>
                                    <span>safira@suksesabadi.com (Keuangan)</span>
                                </div>
                                <span class="demo-btn-fill">Pilih</span>
                            </div>
                            <div class="demo-item" onclick="fillLogin('admin@suksesabadi.com')">
                                <div class="demo-info">
                                    <strong>Admin Sukses Abadi</strong>
                                    <span>admin@suksesabadi.com (Admin)</span>
                                </div>
                                <span class="demo-btn-fill">Pilih</span>
                            </div>
                        </div>
                    </div>
                </main>

                <footer class="footer-note">
                    &copy; {{ date('Y') }} Toko Sukses Abadi. Hak cipta dilindungi.
                </footer>
            </div>
        </div>
    </div>

    <script>
        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
            document.getElementById('email').focus();
        }
    </script>
</body>

</html>