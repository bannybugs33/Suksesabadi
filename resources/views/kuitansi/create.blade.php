<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Tambah Kuitansi Baru | BukuTempo</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f8f9fa;
            color: #292930;
            font-family: Arial, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 220px;
            padding: 24px 16px;
            background: #91283b;
            color: white;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .brand {
            padding: 0 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-text {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.1;
        }

        .brand-text small {
            display: block;
            margin-top: 3px;
            font-size: 7.5px;
            font-weight: 600;
            letter-spacing: 1.5px;
            color: #fce7eb;
        }

        .menu-label {
            margin: 28px 10px 10px;
            font-size: 10px;
            color: #f0c7cf;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-menu a,
        .sidebar-menu span {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: #fce7eb;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu a.menu-active {
            background: rgba(255, 255, 255, 0.16);
            color: white;
            font-weight: 600;
        }

        .menu-parent-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px 4px;
            color: #fce7eb;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .menu-parent-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .menu-sub-link {
            display: block;
            padding: 4px 12px 6px 38px;
            font-size: 12px;
            color: #fce7eb;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .menu-sub-link:hover {
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar-spacer {
            flex: 1;
            min-height: 24px;
        }

        /* Sidebar Sync Info Box */
        .sync-info-box {
            background: rgba(0, 0, 0, 0.18);
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        .sync-info-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #f8d5dc;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .sync-dot {
            width: 7px;
            height: 7px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
        }

        .sync-info-label {
            font-size: 10px;
            color: #e8a2af;
        }

        .sync-info-time {
            font-size: 11px;
            color: #ffffff;
            font-weight: 600;
            margin-top: 1px;
        }

        /* Sidebar User Profile */
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 6px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #ffffff;
        }

        .user-role {
            font-size: 10.5px;
            color: #f0c7cf;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-icon-btn {
            background: none;
            border: none;
            color: #fce7eb;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .logout-icon-btn:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        /* Main Content Layout */
        .main-content {
            margin-left: 220px;
            padding: 32px 40px 60px;
            min-height: 100vh;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 30px;
            font-weight: 700;
            color: #1c1d22;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            font-size: 13.5px;
            color: #6b7082;
            margin-top: 4px;
        }

        /* Stepper Bar */
        .stepper-card {
            background: #ffffff;
            border: 1px solid #eef0f4;
            border-radius: 10px;
            padding: 14px 28px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .step-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step-item.active .step-circle {
            background: #91283b;
            color: #ffffff;
        }

        .step-item.active .step-label {
            font-size: 13px;
            font-weight: 600;
            color: #1c1d22;
        }

        .step-item.inactive .step-circle {
            background: #edf0f4;
            color: #8c909e;
        }

        .step-item.inactive .step-label {
            font-size: 13px;
            font-weight: 500;
            color: #8c909e;
        }

        /* Content Grid */
        .form-layout-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1100px) {
            .form-layout-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Form Cards */
        .form-card {
            background: #ffffff;
            border: 1px solid #eef0f4;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            margin-bottom: 20px;
        }

        .form-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #1c1d22;
            margin-bottom: 20px;
        }

        .form-row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        @media (max-width: 680px) {
            .form-row-2col {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #3b3e4c;
            margin-bottom: 6px;
        }

        /* Inputs with Icons */
        .input-icon-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-wrap svg {
            position: absolute;
            left: 12px;
            color: #838694;
            pointer-events: none;
            flex-shrink: 0;
        }

        .form-input {
            width: 100%;
            height: 42px;
            border: 1px solid #d8dbe2;
            border-radius: 8px;
            padding: 0 12px 0 38px;
            font-size: 13px;
            font-family: inherit;
            color: #292930;
            background: #ffffff;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .form-input:hover {
            border-color: #b0b4be;
            background: #fafafc;
        }

        .form-input:focus {
            outline: none;
            border-color: #91283b;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.1);
        }

        /* Custom Dropdown Select */
        .form-select {
            width: 100%;
            height: 42px;
            border: 1px solid #d8dbe2;
            border-radius: 8px;
            padding: 0 38px 0 14px;
            font-size: 13px;
            font-family: inherit;
            color: #292930;
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23555865' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 14px 14px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            cursor: pointer;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .form-select:hover {
            border-color: #b0b4be;
            background-color: #fafafc;
        }

        .form-select:focus {
            outline: none;
            border-color: #91283b;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.1);
        }

        /* Textarea */
        .form-textarea {
            width: 100%;
            min-height: 96px;
            border: 1px solid #d8dbe2;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 13px;
            font-family: inherit;
            color: #292930;
            background: #ffffff;
            resize: vertical;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .form-textarea:hover {
            border-color: #b0b4be;
            background: #fafafc;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #91283b;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.1);
        }

        .form-helper {
            font-size: 11px;
            color: #8b8e9c;
            margin-top: 4px;
        }

        /* Alert Banner */
        .alert-pengingat {
            background: #fbf5e7;
            border: 1px solid #f4e3b7;
            border-radius: 8px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #634304;
            font-size: 12.5px;
            line-height: 1.4;
            margin-top: 8px;
        }

        .alert-pengingat svg {
            color: #b45309;
            flex-shrink: 0;
        }

        /* Upload Dropzone */
        .dropzone-box {
            border: 1.5px dashed #d8dbe2;
            border-radius: 10px;
            background: #fafafc;
            padding: 32px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
        }

        .dropzone-box:hover,
        .dropzone-box.dragover {
            border-color: #91283b;
            background: #fffbfa;
        }

        .dropzone-cloud-icon {
            color: #91283b;
            margin-bottom: 8px;
        }

        .dropzone-title {
            font-size: 14px;
            font-weight: 700;
            color: #1c1d22;
        }

        .dropzone-subtitle {
            font-size: 11px;
            color: #8b8e9c;
            margin: 4px 0 14px;
        }

        .btn-pilih-file {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 1px solid #d8dbe2;
            border-radius: 20px;
            padding: 6px 18px;
            font-size: 12px;
            font-weight: 600;
            color: #292930;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-pilih-file:hover {
            border-color: #91283b;
            color: #91283b;
            background: #fafafc;
        }

        /* Upload Tips */
        .upload-tips {
            margin-top: 18px;
        }

        .upload-tips-title {
            font-size: 12px;
            font-weight: 700;
            color: #20222a;
            margin-bottom: 8px;
        }

        .upload-tips-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .upload-tips-list li {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: #555865;
        }

        .upload-tips-list svg {
            color: #696c7a;
            flex-shrink: 0;
        }

        /* File Preview Box */
        .preview-box {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            background: #ffffff;
            border: 1px solid #d8dbe2;
            border-radius: 8px;
            margin-top: 12px;
            text-align: left;
        }

        .preview-thumb {
            width: 44px;
            height: 44px;
            border-radius: 6px;
            object-fit: cover;
            background: #f0f1f5;
            flex-shrink: 0;
        }

        .preview-info {
            flex: 1;
            min-width: 0;
        }

        .preview-name {
            font-size: 12px;
            font-weight: 600;
            color: #1c1d22;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .preview-size {
            font-size: 10.5px;
            color: #8b8e9c;
            margin-top: 2px;
        }

        .preview-remove-btn {
            background: none;
            border: none;
            color: #c93548;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px;
        }

        .preview-remove-btn:hover {
            text-decoration: underline;
        }

        /* Ringkasan Arsip Card */
        .ringkasan-arsip-card {
            background: #91283b;
            border-radius: 10px;
            padding: 18px 20px;
            color: #ffffff;
        }

        .ringkasan-arsip-title {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.2px;
            color: #fce7eb;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .ringkasan-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .ringkasan-row:last-child {
            margin-bottom: 0;
        }

        .ringkasan-label {
            color: #fce7eb;
        }

        .ringkasan-value {
            font-weight: 700;
            font-size: 13px;
            color: #ffffff;
        }

        .ringkasan-value.status-highlight {
            color: #ffd166;
        }

        /* Footer Action Bar */
        .form-action-bar {
            margin-top: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .form-required-note {
            font-size: 12px;
            color: #8b8e9c;
        }

        .form-action-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-batal {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 42px;
            padding: 0 24px;
            background: #ffffff;
            border: 1px solid #d8dbe2;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #292930;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-batal:hover {
            border-color: #b0b4be;
            background: #fafafc;
        }

        .btn-simpan {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 24px;
            background: #91283b;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-simpan:hover {
            background: #7a1f30;
        }

        /* Error Banner */
        .form-error-banner {
            background: #fde8e8;
            border: 1px solid #f8b4b4;
            color: #9b1c1c;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .form-error-banner ul {
            margin-left: 20px;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    @include('_sidebar')

    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1 class="page-title">Tambah Kuitansi Baru</h1>
            <p class="page-subtitle">Lengkapi data transaksi agar tempo dan status pembayaran dapat dipantau.</p>
        </header>

        <!-- Stepper Indicator -->
        <div class="stepper-card">
            <div class="step-item active">
                <div class="step-circle">1</div>
                <span class="step-label">Informasi transaksi</span>
            </div>
            <div class="step-item active">
                <div class="step-circle">2</div>
                <span class="step-label">Bukti & pembayaran</span>
            </div>
            <div class="step-item inactive">
                <div class="step-circle">3</div>
                <span class="step-label">Simpan arsip</span>
            </div>
        </div>

        @if ($errors->any())
            <div class="form-error-banner">
                <strong>Mohon periksa kembali isian formulir:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('kuitansi.store') }}" enctype="multipart/form-data" id="kuitansiForm">
            @csrf

            <div class="form-layout-grid">
                <!-- Left Column -->
                <div class="left-col">
                    <!-- Card 1: Informasi kuitansi -->
                    <div class="form-card">
                        <h2 class="form-card-title">Informasi kuitansi</h2>

                        <div class="form-row-2col">
                            <div class="form-group">
                                <label for="tanggalKuitansiInput" class="form-label">Tanggal kuitansi *</label>
                                <div class="input-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <input
                                        type="date"
                                        id="tanggalKuitansiInput"
                                        name="tanggal_kuitansi"
                                        class="form-input"
                                        value="{{ old('tanggal_kuitansi', $tanggalKuitansi) }}"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="namaPihakInput" class="form-label">Nama pelanggan / supplier *</label>
                                <div class="input-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                    </svg>
                                    <input
                                        type="text"
                                        id="namaPihakInput"
                                        name="nama_pihak"
                                        class="form-input"
                                        placeholder="Contoh: Warung Bu Sari"
                                        value="{{ old('nama_pihak') }}"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="form-row-2col">
                            <div class="form-group">
                                <label for="jenisSelect" class="form-label">Jenis kuitansi *</label>
                                <select id="jenisSelect" name="jenis" class="form-select" required>
                                    <option value="piutang" {{ old('jenis', 'piutang') === 'piutang' ? 'selected' : '' }}>
                                        Piutang — pelanggan berutang
                                    </option>
                                    <option value="utang" {{ old('jenis') === 'utang' ? 'selected' : '' }}>
                                        Utang — toko berutang
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="nominalTransaksiDisplay" class="form-label">Nominal transaksi *</label>
                                <div class="input-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                                        <circle cx="12" cy="12" r="2"></circle>
                                        <path d="M6 12h.01M18 12h.01"></path>
                                    </svg>
                                    <input
                                        type="text"
                                        id="nominalTransaksiDisplay"
                                        class="form-input"
                                        placeholder="Rp 0"
                                        value="{{ old('nominal_transaksi') ? 'Rp ' . number_format((float) old('nominal_transaksi'), 0, ',', '.') : 'Rp 0' }}"
                                        required
                                    >
                                    <input type="hidden" id="nominalTransaksiHidden" name="nominal_transaksi" value="{{ old('nominal_transaksi', 0) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="catatanTextarea" class="form-label">Catatan transaksi</label>
                            <textarea
                                id="catatanTextarea"
                                name="catatan"
                                class="form-textarea"
                                maxlength="500"
                                placeholder="Tuliskan barang, layanan, atau keterangan transaksi..."
                            >{{ old('catatan') }}</textarea>
                            <div class="form-helper" id="catatanCounter">Maksimal 500 karakter</div>
                        </div>
                    </div>

                    <!-- Card 2: Pembayaran & jatuh tempo -->
                    <div class="form-card">
                        <h2 class="form-card-title">Pembayaran & jatuh tempo</h2>

                        <div class="form-row-2col">
                            <div class="form-group">
                                <label for="statusSelect" class="form-label">Status pembayaran awal *</label>
                                <select id="statusSelect" name="status_pembayaran" class="form-select" required>
                                    <option value="belum_lunas" {{ old('status_pembayaran', 'belum_lunas') === 'belum_lunas' ? 'selected' : '' }}>
                                        Belum lunas
                                    </option>
                                    <option value="dp" {{ old('status_pembayaran') === 'dp' ? 'selected' : '' }}>
                                        DP
                                    </option>
                                    <option value="cicilan" {{ old('status_pembayaran') === 'cicilan' ? 'selected' : '' }}>
                                        Cicilan
                                    </option>
                                    <option value="lunas" {{ old('status_pembayaran') === 'lunas' ? 'selected' : '' }}>
                                        Lunas
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="nominalDibayarDisplay" class="form-label">Nominal dibayar</label>
                                <div class="input-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                                        <circle cx="12" cy="12" r="2"></circle>
                                        <path d="M6 12h.01M18 12h.01"></path>
                                    </svg>
                                    <input
                                        type="text"
                                        id="nominalDibayarDisplay"
                                        class="form-input"
                                        placeholder="Rp 0"
                                        value="{{ old('nominal_dibayar') ? 'Rp ' . number_format((float) old('nominal_dibayar'), 0, ',', '.') : 'Rp 0' }}"
                                    >
                                    <input type="hidden" id="nominalDibayarHidden" name="nominal_dibayar" value="{{ old('nominal_dibayar', 0) }}">
                                </div>
                                <div class="form-helper">Diisi jika status DP atau cicilan</div>
                            </div>
                        </div>

                        <div class="form-row-2col">
                            <div class="form-group">
                                <label for="jatuhTempoInput" class="form-label">Tanggal jatuh tempo *</label>
                                <div class="input-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <input
                                        type="date"
                                        id="jatuhTempoInput"
                                        name="tanggal_jatuh_tempo"
                                        class="form-input"
                                        value="{{ old('tanggal_jatuh_tempo', $tanggalJatuhTempo) }}"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="pengingatSelect" class="form-label">Pengingat</label>
                                <select id="pengingatSelect" class="form-select">
                                    <option value="3" selected>3 hari sebelum tempo</option>
                                    <option value="1">1 hari sebelum tempo</option>
                                    <option value="7">7 hari sebelum tempo</option>
                                    <option value="0">Tepat hari tempo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Alert Banner -->
                        <div class="alert-pengingat" id="alertPengingatBox">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <span id="alertPengingatText">
                                BukuTempo akan menampilkan pengingat di dashboard mulai <strong id="tglPengingatDisplay">10 Okt 2026</strong> sampai transaksi dilunasi.
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="right-col">
                    <!-- Card 1: Unggah foto kuitansi -->
                    <div class="form-card">
                        <h2 class="form-card-title">Unggah foto kuitansi</h2>

                        <div
                            class="dropzone-box"
                            id="dropzoneBox"
                            onclick="document.getElementById('buktiInput').click()"
                        >
                            <svg class="dropzone-cloud-icon" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"></path>
                                <polyline points="12 12 12 16"></polyline>
                                <polyline points="9 13 12 10 15 13"></polyline>
                            </svg>
                            <div class="dropzone-title">Tarik foto ke sini</div>
                            <div class="dropzone-subtitle">atau klik untuk pilih JPG, PNG, atau PDF hingga 10 MB</div>
                            <button type="button" class="btn-pilih-file">Pilih file</button>

                            <input
                                type="file"
                                name="bukti"
                                id="buktiInput"
                                accept="image/jpeg,image/png,image/webp,application/pdf"
                                style="display: none;"
                                onchange="handleFileSelected(this.files)"
                            >
                        </div>

                        <!-- Preview File -->
                        <div class="preview-box" id="previewBox">
                            <img src="" id="previewImg" class="preview-thumb" alt="Preview bukti" style="display: none;">
                            <div id="previewPdfIcon" style="display: none; width: 44px; height: 44px; background: #fee2e2; color: #dc2626; border-radius: 6px; align-items: center; justify-content: center;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            </div>
                            <div class="preview-info">
                                <div class="preview-name" id="previewFileName">kuitansi.jpg</div>
                                <div class="preview-size" id="previewFileSize">1.2 MB</div>
                            </div>
                            <button type="button" class="preview-remove-btn" onclick="removeSelectedFile(event)">Hapus</button>
                        </div>

                        <div class="upload-tips">
                            <div class="upload-tips-title">Agar bukti mudah dibaca</div>
                            <ul class="upload-tips-list">
                                <li>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Pastikan seluruh kuitansi terlihat</span>
                                </li>
                                <li>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Hindari bayangan dan pantulan</span>
                                </li>
                                <li>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span>Gunakan foto dengan fokus tajam</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 2: RINGKASAN ARSIP -->
                    <div class="ringkasan-arsip-card">
                        <div class="ringkasan-arsip-title">RINGKASAN ARSIP</div>
                        <div class="ringkasan-row">
                            <span class="ringkasan-label">Nomor sementara</span>
                            <span class="ringkasan-value" id="ringkasanNomorDisplay">{{ $nomorSementara }}</span>
                        </div>
                        <div class="ringkasan-row">
                            <span class="ringkasan-label">Status awal</span>
                            <span class="ringkasan-value status-highlight" id="ringkasanStatusDisplay">Belum lunas</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Bar -->
            <div class="form-action-bar">
                <div class="form-required-note">* Data wajib diisi sebelum disimpan</div>
                <div class="form-action-buttons">
                    <a href="{{ route('dashboard') }}" class="btn-batal">Batal</a>
                    <button type="submit" class="btn-simpan">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Simpan kuitansi
                    </button>
                </div>
            </div>
        </form>
    </main>

    <script>
        // Bulan Indonesia
        const bulanIndo = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        function formatTanggalIndo(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const year = parts[0];
            const monthIdx = parseInt(parts[1], 10) - 1;
            const day = parseInt(parts[2], 10);
            return `${day} ${bulanIndo[monthIdx] || ''} ${year}`;
        }

        // Hitung alert pengingat
        function updateAlertPengingat() {
            const jatuhTempoVal = document.getElementById('jatuhTempoInput').value;
            const pengingatDays = parseInt(document.getElementById('pengingatSelect').value, 10) || 0;

            if (!jatuhTempoVal) return;

            const d = new Date(jatuhTempoVal);
            if (isNaN(d.getTime())) return;

            d.setDate(d.getDate() - pengingatDays);
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            const formattedDate = formatTanggalIndo(`${yyyy}-${mm}-${dd}`);

            document.getElementById('tglPengingatDisplay').textContent = formattedDate;
        }

        // Live formatting Rupiah
        function setupRupiahInput(displayId, hiddenId) {
            const displayEl = document.getElementById(displayId);
            const hiddenEl = document.getElementById(hiddenId);

            function updateFormat() {
                let raw = displayEl.value.replace(/[^\d]/g, '');
                if (!raw || raw === '0') {
                    hiddenEl.value = '0';
                    displayEl.value = 'Rp 0';
                    return;
                }
                const num = parseInt(raw, 10);
                hiddenEl.value = num;
                displayEl.value = 'Rp ' + num.toLocaleString('id-ID');
            }

            displayEl.addEventListener('input', updateFormat);
            displayEl.addEventListener('focus', function() {
                if (displayEl.value === 'Rp 0') {
                    displayEl.value = 'Rp ';
                }
            });
            displayEl.addEventListener('blur', function() {
                if (displayEl.value.trim() === 'Rp' || !displayEl.value.trim()) {
                    displayEl.value = 'Rp 0';
                    hiddenEl.value = '0';
                }
            });
        }

        setupRupiahInput('nominalTransaksiDisplay', 'nominalTransaksiHidden');
        setupRupiahInput('nominalDibayarDisplay', 'nominalDibayarHidden');

        // Status change sync to Ringkasan Arsip
        const statusSelect = document.getElementById('statusSelect');
        const ringkasanStatusDisplay = document.getElementById('ringkasanStatusDisplay');

        function updateRingkasanStatus() {
            const val = statusSelect.value;
            let label = 'Belum lunas';
            if (val === 'dp') label = 'DP';
            else if (val === 'cicilan') label = 'Cicilan';
            else if (val === 'lunas') label = 'Lunas';

            ringkasanStatusDisplay.textContent = label;

            // Jika lunas, otomatis isi nominal dibayar sama dengan nominal transaksi
            const nominalTransaksi = parseInt(document.getElementById('nominalTransaksiHidden').value, 10) || 0;
            if (val === 'lunas' && nominalTransaksi > 0) {
                document.getElementById('nominalDibayarHidden').value = nominalTransaksi;
                document.getElementById('nominalDibayarDisplay').value = 'Rp ' + nominalTransaksi.toLocaleString('id-ID');
            }
        }

        statusSelect.addEventListener('change', updateRingkasanStatus);

        // Update nomor sementara saat tanggal kuitansi diganti
        const tglKuitansiInput = document.getElementById('tanggalKuitansiInput');
        tglKuitansiInput.addEventListener('change', function() {
            const val = this.value;
            if (!val) return;
            const parts = val.split('-');
            if (parts.length === 3) {
                const yy = parts[0].slice(-2);
                const mm = parts[1];
                const dd = parts[2];
                document.getElementById('ringkasanNomorDisplay').textContent = `KWT-${yy}${mm}${dd}`;

                // Set default jatuh tempo H+14 jika belum diubah
                const jtDate = new Date(this.value);
                jtDate.setDate(jtDate.getDate() + 14);
                const yyyy = jtDate.getFullYear();
                const jtMm = String(jtDate.getMonth() + 1).padStart(2, '0');
                const jtDd = String(jtDate.getDate()).padStart(2, '0');
                document.getElementById('jatuhTempoInput').value = `${yyyy}-${jtMm}-${jtDd}`;
                updateAlertPengingat();
            }
        });

        document.getElementById('jatuhTempoInput').addEventListener('change', updateAlertPengingat);
        document.getElementById('pengingatSelect').addEventListener('change', updateAlertPengingat);

        // Dropzone Drag & Drop
        const dropzone = document.getElementById('dropzoneBox');
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('dragover');
            }, false);
        });

        dropzone.addEventListener('drop', e => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                document.getElementById('buktiInput').files = files;
                handleFileSelected(files);
            }
        });

        function handleFileSelected(files) {
            if (!files || files.length === 0) return;
            const file = files[0];
            const previewBox = document.getElementById('previewBox');
            const previewImg = document.getElementById('previewImg');
            const previewPdfIcon = document.getElementById('previewPdfIcon');
            const previewFileName = document.getElementById('previewFileName');
            const previewFileSize = document.getElementById('previewFileSize');

            previewFileName.textContent = file.name;
            const sizeInMb = (file.size / (1024 * 1024)).toFixed(1);
            previewFileSize.textContent = `${sizeInMb} MB`;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    previewPdfIcon.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                previewImg.style.display = 'none';
                previewPdfIcon.style.display = 'flex';
            }

            previewBox.style.display = 'flex';
        }

        function removeSelectedFile(e) {
            if (e) e.stopPropagation();
            const input = document.getElementById('buktiInput');
            input.value = '';
            document.getElementById('previewBox').style.display = 'none';
            document.getElementById('previewImg').src = '';
        }

        // Inisialisasi awal
        updateAlertPengingat();
        updateRingkasanStatus();
    </script>
</body>
</html>

