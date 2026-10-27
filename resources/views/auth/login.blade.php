<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - BukuTempo Toko Sukses Abadi</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .login-layout {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* =========================================
           LEFT HERO PANEL (Maroon Theme)
           ========================================= */
        .hero-panel {
            flex: 0 0 42%;
            background: #8e1f34;
            background: linear-gradient(155deg, #94263a 0%, #7e192c 100%);
            padding: 48px 56px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .hero-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon-box {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.16);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            flex-shrink: 0;
        }

        .brand-text-block {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-size: 19px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .brand-tagline {
            font-size: 9.5px;
            font-weight: 700;
            color: #f0c7cf;
            letter-spacing: 1.5px;
            margin-top: 2px;
        }

        .hero-center-content {
            margin: 40px 0;
        }

        .hero-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 0, 0, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 9999px;
            padding: 6px 14px;
            font-size: 10.5px;
            font-weight: 700;
            color: #fce7eb;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #ff8a9e;
        }

        .hero-heading {
            font-size: 40px;
            font-weight: 700;
            line-height: 1.22;
            letter-spacing: -0.6px;
            color: #ffffff;
            margin-bottom: 18px;
        }

        .hero-description {
            font-size: 14.5px;
            line-height: 1.6;
            color: #fce7eb;
            max-width: 440px;
            margin-bottom: 36px;
        }

        /* Preview Widget Card */
        .preview-widget {
            background: rgba(0, 0, 0, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 24px;
            max-width: 430px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .preview-widget-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .widget-metric-label {
            font-size: 10.5px;
            font-weight: 700;
            color: #f5cdd4;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .widget-metric-value {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-top: 4px;
        }

        .widget-trend-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 9999px;
            padding: 4px 10px;
            font-size: 11.5px;
            font-weight: 600;
            color: #ffffff;
        }

        /* Bar Chart */
        .preview-bar-chart {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            height: 90px;
            padding-bottom: 6px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chart-bar-item {
            flex: 1;
            background: rgba(255, 255, 255, 0.22);
            border-radius: 4px 4px 0 0;
            transition: all 0.2s ease;
        }

        .chart-bar-item.is-featured {
            background: #bd3149;
            box-shadow: 0 0 14px rgba(189, 49, 73, 0.6);
        }

        /* Inner Floating Notification */
        .preview-floating-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .floating-card-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .floating-bell-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #fef3c7;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .floating-card-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .floating-card-subtitle {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 2px;
        }

        .floating-card-action {
            font-size: 11.5px;
            font-weight: 700;
            color: #91283b;
            text-decoration: none;
            padding: 4px 8px;
            cursor: default;
        }

        .hero-footer {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 0.2px;
        }

        /* =========================================
           RIGHT FORM PANEL (Clean White Theme)
           ========================================= */
        .form-panel {
            flex: 1;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .form-card {
            width: 100%;
            max-width: 450px;
            background: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 24px;
            padding: 42px 40px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.06), 0 0 1px rgba(15, 23, 42, 0.08);
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-title {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.4px;
            line-height: 1.25;
        }

        .form-subtitle {
            font-size: 13.5px;
            color: #64748b;
            margin-top: 6px;
            line-height: 1.5;
        }

        /* Alert Banners */
        .auth-alert-banner {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 22px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .auth-alert-banner.is-success {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .alert-icon-box {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-body {
            flex: 1;
        }

        .alert-heading {
            font-size: 12.5px;
            font-weight: 700;
            color: #991b1b;
            line-height: 1.3;
        }

        .auth-alert-banner.is-success .alert-heading {
            color: #166534;
        }

        .alert-text {
            font-size: 11.5px;
            color: #b91c1c;
            margin-top: 2px;
            line-height: 1.4;
        }

        .auth-alert-banner.is-success .alert-text {
            color: #15803d;
        }

        /* Form Inputs */
        .form-row {
            margin-bottom: 18px;
        }

        .label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .input-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .label-row .input-label {
            margin-bottom: 0;
        }

        .forgot-password-link {
            font-size: 12px;
            font-weight: 600;
            color: #b92743;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .forgot-password-link:hover {
            color: #94263a;
            text-decoration: underline;
        }

        .input-field-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-left-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .text-input {
            width: 100%;
            height: 46px;
            padding: 10px 14px 10px 42px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
            background: #ffffff;
            outline: none;
            font-family: inherit;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .text-input:focus {
            border-color: #91283b;
            box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.1);
        }

        .text-input.has-right-toggle {
            padding-right: 44px;
        }

        .text-input.is-error {
            border-color: #ef4444;
            background: #fffcfc;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            padding: 6px;
            cursor: pointer;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color 0.15s ease;
        }

        .password-toggle:hover {
            color: #475569;
        }

        .checkbox-row {
            margin: 18px 0 22px;
            display: flex;
            align-items: center;
        }

        .checkbox-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #91283b;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Submit Button */
        .btn-login-submit {
            width: 100%;
            height: 48px;
            background: #b92743;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
        }

        .btn-login-submit:hover {
            background: #a31e36;
            box-shadow: 0 4px 12px rgba(185, 39, 67, 0.25);
        }

        .btn-login-submit:active {
            transform: scale(0.99);
        }

        .security-badge {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 18px;
        }

        .help-contact-note {
            text-align: center;
            font-size: 12px;
            color: #64748b;
            margin-top: 24px;
        }

        .help-contact-note a {
            color: #475569;
            font-weight: 500;
            text-decoration: none;
        }

        .help-contact-note a:hover {
            text-decoration: underline;
            color: #0f172a;
        }

        /* =========================================
           RESPONSIVE BREAKPOINTS
           ========================================= */
        @media (max-width: 960px) {
            .hero-panel {
                display: none;
            }

            .form-panel {
                padding: 32px 16px;
            }

            .form-card {
                padding: 32px 24px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            }
        }
    </style>
</head>
<body>
    <div class="login-layout">
        <!-- =========================================
             LEFT HERO PANEL
             ========================================= -->
        <aside class="hero-panel">
            <!-- Brand Header -->
            <div class="hero-brand">
                <div class="brand-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <div class="brand-text-block">
                    <span class="brand-name">BukuTempo</span>
                    <span class="brand-tagline">CATAT • INGAT • LUNAS</span>
                </div>
            </div>

            <!-- Hero Center Content -->
            <div class="hero-center-content">
                <div class="hero-status-pill">
                    <span class="status-dot"></span>
                    <span>PENCATATAN TOKO YANG TENANG</span>
                </div>

                <h1 class="hero-heading">
                    Utang–piutang rapi,<br>
                    tempo tak lagi terlewat.
                </h1>

                <p class="hero-description">
                    Simpan kuitansi, pantau pembayaran, dan dapatkan pengingat jatuh tempo dalam satu tempat yang mudah dipahami.
                </p>

                @php
                    $ringkasRupiah = fn (float $nilai): string => $nilai >= 1000000
                        ? 'Rp'.number_format($nilai / 1000000, 2, ',', '.').' jt'
                        : 'Rp'.number_format($nilai, 0, ',', '.');
                    $perubahan = $grafikPiutang['perubahan'];
                @endphp

                <!-- Preview Widget Card (data dari database, hanya angka agregat) -->
                <div class="preview-widget">
                    <div class="preview-widget-header">
                        <div>
                            <div class="widget-metric-label">PIUTANG BULAN INI</div>
                            <div class="widget-metric-value">{{ $ringkasRupiah($grafikPiutang['total']) }}</div>
                        </div>
                        @if (! is_null($perubahan))
                            <div class="widget-trend-badge">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    @if ($perubahan >= 0)
                                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                        <polyline points="17 6 23 6 23 12"></polyline>
                                    @else
                                        <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline>
                                        <polyline points="17 18 23 18 23 12"></polyline>
                                    @endif
                                </svg>
                                <span>{{ $perubahan >= 0 ? '+' : '−' }}{{ number_format(abs($perubahan), 1, ',', '.') }}%</span>
                            </div>
                        @endif
                    </div>

                    <!-- Mini Bar Chart -->
                    <div class="preview-bar-chart">
                        @foreach ($grafikPiutang['bars'] as $bar)
                            <div class="chart-bar-item {{ $bar['aktif'] ? 'is-featured' : '' }}" style="height: {{ (int) round($bar['tinggi'] * 0.84) }}px;" title="{{ $bar['label'] }}"></div>
                        @endforeach
                    </div>

                    <!-- Inner Floating Notification Card -->
                    <div class="preview-floating-card">
                        <div class="floating-card-left">
                            <div class="floating-bell-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                            </div>
                            <div>
                                @if ($pengingat['jumlah'] > 0)
                                    <div class="floating-card-title">{{ $pengingat['jumlah'] }} kuitansi perlu perhatian</div>
                                    <div class="floating-card-subtitle">Rp{{ number_format($pengingat['sisa'], 0, ',', '.') }} belum diselesaikan</div>
                                @else
                                    <div class="floating-card-title">Tidak ada kuitansi yang perlu perhatian</div>
                                    <div class="floating-card-subtitle">Belum ada tagihan yang mendekati jatuh tempo</div>
                                @endif
                            </div>
                        </div>
                        @if ($pengingat['jumlah'] > 0)
                            <a href="{{ route('jatuh-tempo.index') }}" class="floating-card-action" style="cursor: pointer;">Lihat</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer Left -->
            <footer class="hero-footer">
                &copy; 2026 BukuTempo · Dibuat untuk usaha Indonesia
            </footer>
        </aside>

        <!-- =========================================
             RIGHT FORM PANEL
             ========================================= -->
        <main class="form-panel">
            <div class="form-card">
                <header class="form-header">
                    <h2 class="form-title">Selamat datang kembali</h2>
                    <p class="form-subtitle">Masuk sebagai owner untuk mengelola arsip dan pembayaran toko.</p>
                </header>

                <!-- Alert: Credentials Error -->
                @if ($errors->any())
                    <div class="auth-alert-banner" role="alert">
                        <div class="alert-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div class="alert-body">
                            <div class="alert-heading">Kredensial tidak sesuai</div>
                            <div class="alert-text">
                                {{ $errors->first('email') ?: ($errors->first('username') ?: 'Username atau password yang Anda masukkan salah. Silakan periksa kembali.') }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Alert: Status Notification -->
                @if (session('status'))
                    <div class="auth-alert-banner is-success" role="alert">
                        <div class="alert-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="alert-body">
                            <div class="alert-heading">Berhasil</div>
                            <div class="alert-text">{{ session('status') }}</div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <!-- Username Field -->
                    <div class="form-row">
                        <label for="username" class="input-label">Username</label>
                        <div class="input-field-wrapper">
                            <span class="input-left-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="text-input {{ $errors->any() ? 'is-error' : '' }}"
                                value="{{ old('username', old('email', 'andi.owner')) }}"
                                placeholder="andi.owner"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-row">
                        <div class="label-row">
                            <label for="password" class="input-label">Password</label>
                            <a href="#lupa-password" class="forgot-password-link" onclick="alert('Silakan hubungi bantuan@bukutempo.id untuk mereset kata sandi Anda.'); return false;">Lupa password?</a>
                        </div>
                        <div class="input-field-wrapper">
                            <span class="input-left-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="text-input has-right-toggle {{ $errors->any() ? 'is-error' : '' }}"
                                placeholder="••••••••"
                                value="password"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility()" title="Tampilkan / Sembunyikan Password">
                                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="eyeOffIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Checkbox -->
                    <div class="checkbox-row">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login-submit">
                        <span>Masuk ke BukuTempo</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>

                    <!-- Security Footnote -->
                    <div class="security-badge">
                        Dilindungi dengan koneksi aman dan terenkripsi
                    </div>
                </form>
            </div>

            <!-- Help Contact -->
            <div class="help-contact-note">
                Butuh bantuan? Hubungi <a href="mailto:bantuan@bukutempo.id">bantuan@bukutempo.id</a>
            </div>
        </main>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }
    </script>
</body>
</html>
