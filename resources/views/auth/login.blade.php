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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: #91283b;
            color: #ffffff;
            border-radius: 16px;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
            box-shadow: 0 8px 16px rgba(145, 40, 59, 0.2);
        }

        .brand-title {
            font-size: 26px;
            font-weight: 700;
            color: #91283b;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 11px;
            font-weight: 600;
            color: #8b8e9c;
            letter-spacing: 1.5px;
            margin-top: 4px;
            text-transform: uppercase;
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
    <div class="login-container">
        <header class="brand-header">
            <div class="brand-badge">BT</div>
            <h1 class="brand-title">BukuTempo</h1>
            <p class="brand-subtitle">CATAT · INGAT · LUNAS — TOKO SUKSES ABADI</p>
        </header>

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
                        placeholder="nama@suksesabadi.com"
                    >
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
                        value="password"
                    >
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

    <script>
        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
            document.getElementById('email').focus();
        }
    </script>
</body>
</html>

