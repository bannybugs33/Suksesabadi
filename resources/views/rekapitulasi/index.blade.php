<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Rekapitulasi BukuTempo</title>
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 36px;
        background: #f7f8fa;
        color: #292930;
        font-family: Arial, sans-serif;
        font-size: 14px;
        line-height: 1.5;
    }

    h1 {
        margin: 0 0 24px;
        font-size: 28px;
        font-weight: 600;
    }

    form {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 20px;
        background: white;
        border: 1px solid #e6e7ec;
        border-radius: 12px;
    }

    input,
    select,
    button {
        min-height: 40px;
        padding: 8px 12px;
        border: 1px solid #d8dbe2;
        border-radius: 7px;
        background: white;
        color: #292930;
        font: inherit;
    }

    button {
        padding-inline: 22px;
        border-color: #91283b;
        background: #91283b;
        color: white;
        cursor: pointer;
    }

    button:hover {
        background: #782032;
    }

    a {
        color: #a52b43;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    input:focus-visible,
    select:focus-visible,
    button:focus-visible,
    a:focus-visible {
        outline: 3px solid #e6a4b1;
        outline-offset: 3px;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin: 24px 0;
    }

    .summary-card {
        padding: 22px;
        background: white;
        border: 1px solid #e6e7ec;
        border-radius: 12px;
        box-shadow: 0 4px 12px #20203005;
    }

    .summary-card > div {
        color: #666875;
    }

    .summary-card strong {
        display: block;
        margin-top: 12px;
        font-size: 24px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .table-wrap {
        overflow-x: auto;
        background: white;
        border: 1px solid #e6e7ec;
        border-radius: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
    }

    th,
    td {
        padding: 16px;
        border: none;
        border-bottom: 1px solid #eceef2;
        text-align: left;
    }

    th {
        background: #f0f1f5;
        color: #696b78;
        font-size: 12px;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    tbody tr:hover {
        background: #fcf5f6;
    }

    @media (max-width: 1000px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        body {
            padding: 20px;
        }

        h1 {
            font-size: 23px;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
    body {
    padding: 0;
}

.sidebar {
    position: fixed;
    inset: 0 auto 0 0;
    width: 220px;
    padding: 28px 16px;
    background: #91283b;
    color: white;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.brand {
    padding: 0 12px;
    font-size: 23px;
    font-weight: 600;
}

.brand small {
    display: block;
    margin-top: 4px;
    font-size: 9px;
    font-weight: 400;
    letter-spacing: 1px;
}

.menu-label {
    margin: 36px 12px 12px;
    font-size: 10px;
    color: #f0c7cf;
}

.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.sidebar-menu a,
.sidebar-menu span {
    display: block;
    padding: 12px 14px;
    border-radius: 8px;
    color: white;
    font-size: 14px;
}

.sidebar-menu .menu-disabled {
    color: #e8bcc5;
}

.sidebar-menu .menu-active {
    background: #b3374f;
    font-weight: 600;
}

.sidebar-menu a:hover {
    background: #a93249;
    text-decoration: none;
}

.sidebar-user {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: auto;
    padding-top: 24px;
    font-size: 12px;
}

.sidebar-user small {
    display: block;
    margin-top: 3px;
    font-size: 10px;
    color: #f0c7cf;
}

.avatar {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: white;
    color: #91283b;
}

.sidebar-logout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    min-height: 34px;
    margin-top: 14px;
    padding: 6px 12px;
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 6px;
    color: white;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s ease;
}

.sidebar-logout-btn:hover {
    background: rgba(0, 0, 0, 0.35);
}

.main-content {
    margin-left: 220px;
    padding: 36px;
}

@media (max-width: 760px) {
    .sidebar {
        position: static;
        width: 100%;
        padding: 20px;
    }

    .menu-label {
        margin-top: 20px;
    }

    .sidebar-user {
        margin-top: 20px;
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
    }
}
.composition-card {
    margin: 24px 0;
    padding: 24px;
    background: white;
    border: 1px solid #e6e7ec;
    border-radius: 12px;
}

.composition-card h2 {
    margin: 0;
    font-size: 17px;
}

.composition-note {
    margin: 8px 0 20px;
    color: #717381;
    font-size: 12px;
}

.composition-content {
    display: flex;
    align-items: center;
    gap: 40px;
}

.donut {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 160px;
    height: 160px;
    border-radius: 50%;
}

.donut-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 116px;
    height: 116px;
    border-radius: 50%;
    background: white;
}

.donut-center strong {
    font-size: 28px;
    font-weight: 500;
}

.donut-center span {
    font-size: 12px;
    color: #717381;
}

.composition-legend {
    flex: 1;
    max-width: 500px;
    width: 100%;
}

.legend-row {
    display: grid;
    grid-template-columns: 10px 1fr auto auto;
    align-items: center;
    gap: 14px;
    margin: 16px 0;
    font-size: 13px;
}

.legend-dot {
    width: 9px;
    height: 9px;
    border-radius: 3px;
}

.legend-row strong {
    min-width: 65px;
    text-align: right;
}

@media (max-width: 1000px) {
    .composition-content {
        flex-direction: column;
        gap: 20px;
    }
}
.analysis-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr);
    gap: 20px;
    margin: 24px 0;
    align-items: start;
}

.analysis-grid .composition-card {
    margin: 0;
}

.analysis-grid .composition-content {
    gap: 24px;
    flex-wrap: wrap;
}

.analysis-grid .composition-legend {
    flex-basis: 240px;
    min-width: 0;
}

.position-card {
    padding: 24px;
    border-radius: 12px;
    background: #91283b;
    color: white;
}

.position-card h2 {
    margin: 0;
    font-size: 16px;
}

.position-value {
    margin: 20px 0;
    font-size: 30px;
    overflow-wrap: anywhere;
}

.position-card p {
    color: #f1cbd3;
    font-size: 12px;
}

.position-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 8px;
    padding: 12px 0;
    border-bottom: 1px solid #ffffff30;
    font-size: 13px;
}

.position-card .position-note {
    margin-bottom: 0;
    font-size: 11px;
}

@media (max-width: 1100px) {
    .analysis-grid {
        grid-template-columns: 1fr;
    }
}
.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.status-lunas {
    background: #eeeef2;
    color: #626271;
}

.status-cicilan {
    background: #e2eff6;
    color: #356e8b;
}

.status-dp {
    background: #fff0d2;
    color: #94600d;
}

.status-belum_lunas {
    background: #fbe3e1;
    color: #af3833;
}

form {
    background: #ffffff;
    border: 1px solid #e6e7ec;
    border-radius: 12px;
    padding: 16px 20px;
    margin: 20px 0 24px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 14px;
}

form label {
    font-size: 13px;
    font-weight: 600;
    color: #4b4d58;
}

form input[type="month"] {
    padding: 8px 12px;
    border: 1px solid #d8dbe2;
    border-radius: 8px;
    font-size: 13px;
    background: #ffffff;
    color: #1a1a24;
    outline: none;
    transition: all 0.15s ease;
    cursor: pointer;
    height: 40px;
    box-sizing: border-box;
}

form input[type="month"]:focus {
    border-color: #91283b;
    box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.12);
}

.custom-select-wrap {
    position: relative;
    user-select: none;
}

.custom-select-box {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    height: 40px;
    padding: 0 14px;
    border: 1px solid #d8dbe2;
    border-radius: 8px;
    font-size: 13px;
    background: #ffffff;
    color: #1a1a24;
    cursor: pointer;
    transition: all 0.15s ease;
    min-width: 165px;
    box-sizing: border-box;
}

.custom-select-box:hover {
    border-color: #b0b4be;
    background: #fafafc;
}

.custom-select-wrap.is-open .custom-select-box {
    border-color: #91283b;
    box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.12);
}

.dropdown-chevron {
    color: #717381;
    transition: transform 0.2s ease, color 0.15s ease;
    flex-shrink: 0;
}

.custom-select-wrap.is-open .dropdown-chevron {
    transform: rotate(180deg);
    color: #91283b;
}

.custom-dropdown-menu {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    min-width: 100%;
    width: max-content;
    background: #ffffff;
    border: 1px solid #e6e7ec;
    border-radius: 12px;
    box-shadow: 0 14px 34px -4px rgba(26, 26, 36, 0.14), 0 4px 12px -2px rgba(26, 26, 36, 0.05);
    padding: 6px;
    z-index: 100;
    box-sizing: border-box;
    animation: dropdownFadeIn 0.16s cubic-bezier(0.16, 1, 0.3, 1);
}

.custom-select-wrap.is-open .custom-dropdown-menu {
    display: block;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-6px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.custom-dropdown-item {
    padding: 9px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #292930;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    cursor: pointer;
    transition: all 0.12s ease;
    white-space: nowrap;
}

.custom-dropdown-item:hover {
    background: #fdf2f4;
    color: #91283b;
}

.custom-dropdown-item.is-selected {
    background: #fbf2f4;
    color: #91283b;
    font-weight: 600;
}

.dropdown-check {
    color: #91283b;
    flex-shrink: 0;
}

form button[type="submit"] {
    padding: 8px 16px;
    background: #91283b;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s ease;
}

form button[type="submit"]:hover {
    background: #782032;
}

form a {
    font-size: 13px;
    color: #717381;
    text-decoration: none;
}

form a:hover {
    color: #91283b;
    text-decoration: underline;
}
</style>
</head>
<body>
    <aside class="sidebar">
    <div class="brand">
        BukuTempo
        <small>CATAT · INGAT · LUNAS</small>
    </div>

    <p class="menu-label">MENU UTAMA</p>

    <nav class="sidebar-menu" aria-label="Menu utama">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="menu-disabled">Arsip Kuitansi</span>
        <span class="menu-disabled">Kuitansi Baru</span>

        <a
            href="{{ route('rekapitulasi.index') }}"
            class="menu-active"
            aria-current="page"
        >
            Rekapitulasi
        </a>
    </nav>

    <div class="sidebar-user">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AS', 0, 2)) }}</div>
        <div style="flex: 1; min-width: 0;">
            <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ auth()->user()->name ?? 'Andi Saputra' }}
            </div>
            <small style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->email ?? 'Owner · Toko Sukses Abadi' }}</small>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0; background: none; border: none; box-shadow: none;">
        @csrf
        <button type="submit" class="sidebar-logout-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Keluar (Logout)
        </button>
    </form>
</aside>

<main class="main-content">
    <h1>Rekapitulasi Utang dan Piutang</h1>
    @if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="GET" action="{{ route('rekapitulasi.index') }}">
    <label for="periode">Periode:</label>
    <input
        type="month"
        id="periode"
        name="periode"
        value="{{ request('periode') }}"
    >

    <label>Jenis:</label>
    <div class="custom-select-wrap" onclick="toggleRekapDropdown(this, event)">
        <input type="hidden" id="jenis" name="jenis" value="{{ request('jenis') }}">
        <div class="custom-select-box">
            <span class="custom-select-value">{{ request('jenis') === 'piutang' ? 'Piutang' : (request('jenis') === 'utang' ? 'Utang' : 'Utang & piutang') }}</span>
            <svg class="dropdown-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </div>
        <div class="custom-dropdown-menu" onclick="event.stopPropagation()">
            <div class="custom-dropdown-item {{ !request('jenis') ? 'is-selected' : '' }}" onclick="selectRekapOption(this, '', 'Utang & piutang', event)">
                <span>Utang & piutang</span>
                @if (!request('jenis'))
                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                @endif
            </div>
            <div class="custom-dropdown-item {{ request('jenis') === 'utang' ? 'is-selected' : '' }}" onclick="selectRekapOption(this, 'utang', 'Utang', event)">
                <span>Utang</span>
                @if (request('jenis') === 'utang')
                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                @endif
            </div>
            <div class="custom-dropdown-item {{ request('jenis') === 'piutang' ? 'is-selected' : '' }}" onclick="selectRekapOption(this, 'piutang', 'Piutang', event)">
                <span>Piutang</span>
                @if (request('jenis') === 'piutang')
                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                @endif
            </div>
        </div>
    </div>

    <label>Status:</label>
    <div class="custom-select-wrap" onclick="toggleRekapDropdown(this, event)">
        <input type="hidden" id="status" name="status" value="{{ request('status') }}">
        <div class="custom-select-box">
            <span class="custom-select-value">{{ request('status') === 'belum_lunas' ? 'Belum lunas' : (request('status') === 'dp' ? 'DP' : (request('status') === 'cicilan' ? 'Cicilan' : (request('status') === 'lunas' ? 'Lunas' : 'Semua status'))) }}</span>
            <svg class="dropdown-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </div>
        <div class="custom-dropdown-menu" onclick="event.stopPropagation()">
            <div class="custom-dropdown-item {{ !request('status') ? 'is-selected' : '' }}" onclick="selectRekapOption(this, '', 'Semua status', event)">
                <span>Semua status</span>
                @if (!request('status'))
                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                @endif
            </div>
            <div class="custom-dropdown-item {{ request('status') === 'belum_lunas' ? 'is-selected' : '' }}" onclick="selectRekapOption(this, 'belum_lunas', 'Belum lunas', event)">
                <span>Belum lunas</span>
                @if (request('status') === 'belum_lunas')
                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                @endif
            </div>
            <div class="custom-dropdown-item {{ request('status') === 'dp' ? 'is-selected' : '' }}" onclick="selectRekapOption(this, 'dp', 'DP', event)">
                <span>DP</span>
                @if (request('status') === 'dp')
                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                @endif
            </div>
            <div class="custom-dropdown-item {{ request('status') === 'cicilan' ? 'is-selected' : '' }}" onclick="selectRekapOption(this, 'cicilan', 'Cicilan', event)">
                <span>Cicilan</span>
                @if (request('status') === 'cicilan')
                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                @endif
            </div>
            <div class="custom-dropdown-item {{ request('status') === 'lunas' ? 'is-selected' : '' }}" onclick="selectRekapOption(this, 'lunas', 'Lunas', event)">
                <span>Lunas</span>
                @if (request('status') === 'lunas')
                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                @endif
            </div>
        </div>
    </div>

    <button type="submit">Terapkan</button>

    <a href="{{ route('rekapitulasi.index') }}">Reset filter</a>
</form>
<div class="summary-grid">
    @foreach ([
        'Total nominal piutang' => $totalPiutang,
        'Total nominal utang' => $totalUtang,
        'Total terbayar' => $totalTerbayar,
        'Sisa belum dibayar' => $totalSisa,
    ] as $label => $nilai)
        <div class="summary-card">
            <div>{{ $label }}</div>

            <strong>
                Rp{{ number_format($nilai, 0, ',', '.') }}
            </strong>
        </div>
    @endforeach
</div>
@php
    $daftarStatus = [
        'lunas' => ['label' => 'Lunas', 'warna' => '#7d7c8c'],
        'cicilan' => ['label' => 'Cicilan', 'warna' => '#356e8b'],
        'dp' => ['label' => 'DP', 'warna' => '#b57719'],
        'belum_lunas' => ['label' => 'Belum lunas', 'warna' => '#bd443e'],
    ];

    $jumlahKuitansi = $kuitansi->count();
    $komposisi = [];
    $segmen = [];
    $posisi = 0;

    foreach ($daftarStatus as $status => $info) {
        $jumlah = $kuitansi->where('status_pembayaran', $status)->count();
        $persentase = $jumlahKuitansi > 0
            ? ($jumlah / $jumlahKuitansi) * 100
            : 0;

        $akhir = $posisi + $persentase;

        if ($jumlah > 0) {
            $segmen[] = "{$info['warna']} {$posisi}% {$akhir}%";
        }

        $komposisi[] = [
            'label' => $info['label'],
            'warna' => $info['warna'],
            'jumlah' => $jumlah,
            'persentase' => $persentase,
        ];

        $posisi = $akhir;
    }

    $latarDiagram = $jumlahKuitansi > 0
        ? 'conic-gradient(' . implode(', ', $segmen) . ')'
        : '#e8e9ee';
@endphp

<div class="analysis-grid">
<section class="composition-card" aria-labelledby="judul-komposisi">
    <h2 id="judul-komposisi">Komposisi status pembayaran</h2>
    <p class="composition-note">Berdasarkan jumlah kuitansi sesuai filter.</p>

    <div class="composition-content">
        <div
            class="donut"
            style="background: {{ $latarDiagram }};"
            aria-hidden="true"
        >
            <div class="donut-center">
                <strong>{{ $jumlahKuitansi }}</strong>
                <span>kuitansi</span>
            </div>
        </div>

        <div class="composition-legend">
            @foreach ($komposisi as $status)
                <div class="legend-row">
                    <span
                        class="legend-dot"
                        style="background: {{ $status['warna'] }};"
                    ></span>

                    <span>{{ $status['label'] }}</span>
                    <span>{{ $status['jumlah'] }} kuitansi</span>

                    <strong>
                        {{ number_format($status['persentase'], 2, ',', '.') }}%
                    </strong>
                </div>
            @endforeach
        </div>
    </div>

    @if ($jumlahKuitansi === 0)
        <p class="composition-note">
            Tidak ada kuitansi yang sesuai dengan filter.
        </p>
    @endif
</section>
    <section class="position-card">
        <h2>Selisih nominal piutang dan utang</h2>

        <div class="position-value">
            {{ $totalPiutang > $totalUtang ? '+' : ($totalPiutang < $totalUtang ? '-' : '') }}Rp{{ number_format(abs($totalPiutang - $totalUtang), 0, ',', '.') }}
        </div>

        <p>
            Total nominal piutang dikurangi total nominal utang
            sesuai filter yang dipilih.
        </p>

        <div class="position-row">
            <span>Piutang</span>
            <strong>
                Rp{{ number_format($totalPiutang, 0, ',', '.') }}
            </strong>
        </div>

        <div class="position-row">
            <span>Utang</span>
            <strong>
                Rp{{ number_format($totalUtang, 0, ',', '.') }}
            </strong>
        </div>

        <p class="position-note">
            Selisih ini bukan laba atau saldo kas.
        </p>
    </section>
</div>
    <p>Jumlah kuitansi: {{ $kuitansi->count() }}</p>

    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Nomor kuitansi</th>
                <th>Pelanggan / supplier</th>
                <th>Jenis</th>
                <th>Nominal</th>
                <th>Terbayar</th>
                <th>Sisa</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kuitansi as $item)
                <tr>
                    <td>{{ $item->nomor_kuitansi }}</td>
                    <td>{{ $item->nama_pihak }}</td>
                    <td>{{ ucfirst($item->jenis) }}</td>
                    <td>
                        Rp{{ number_format($item->nominal_transaksi, 0, ',', '.') }}
                    </td>
                    <td>
                        Rp{{ number_format($item->terbayar, 0, ',', '.') }}
                    </td>
                    <td>
                        Rp{{ number_format($item->sisa, 0, ',', '.') }}
                    </td>
                    <td>
                        <span class="status-badge status-{{ $item->status_pembayaran }}">
    {{ $item->status_pembayaran === 'dp'
        ? 'DP'
        : ucfirst(str_replace('_', ' ', $item->status_pembayaran)) }}
</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Belum ada data kuitansi.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
    <tr>
        <th colspan="3">
            TOTAL {{ $kuitansi->count() }} KUITANSI
        </th>

        <th>
            Rp{{ number_format($totalPiutang + $totalUtang, 0, ',', '.') }}
        </th>

        <th>
            Rp{{ number_format($totalTerbayar, 0, ',', '.') }}
        </th>

        <th>
            Rp{{ number_format($totalSisa, 0, ',', '.') }}
        </th>

        <th></th>
    </tr>
</tfoot>
    </table>
    </div>
    <script>
        function toggleRekapDropdown(el, e) {
            if (e) e.stopPropagation();
            const wasOpen = el.classList.contains('is-open');
            document.querySelectorAll('.custom-select-wrap.is-open').forEach(item => item.classList.remove('is-open'));
            if (!wasOpen) el.classList.add('is-open');
        }

        function selectRekapOption(itemEl, val, label, e) {
            if (e) e.stopPropagation();
            const wrap = itemEl.closest('.custom-select-wrap');
            const input = wrap.querySelector('input[type="hidden"]');
            const valSpan = wrap.querySelector('.custom-select-value');
            input.value = val;
            valSpan.textContent = label;
            wrap.classList.remove('is-open');
            wrap.querySelectorAll('.custom-dropdown-item').forEach(el => {
                const isSelected = el === itemEl;
                el.classList.toggle('is-selected', isSelected);
                let check = el.querySelector('.dropdown-check');
                if (isSelected && !check) {
                    el.insertAdjacentHTML('beforeend', '<svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>');
                } else if (!isSelected && check) {
                    check.remove();
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-select-wrap')) {
                document.querySelectorAll('.custom-select-wrap.is-open').forEach(el => el.classList.remove('is-open'));
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.custom-select-wrap.is-open').forEach(el => el.classList.remove('is-open'));
            }
        });
    </script>
</body>
</html>