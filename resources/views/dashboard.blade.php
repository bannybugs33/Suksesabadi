<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BukuTempo Toko Sukses Abadi</title>
    @include('partials.topnav-styles')
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Manrope', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;
            background: #fff;
            color: var(--ink);
            padding: 12px;
            -webkit-font-smoothing: antialiased;
        }

        .shell { background: var(--stage); border-radius: 40px; padding: 16px 18px 18px; max-width: 1240px; margin: 0 auto; }
        .board { margin-top: 14px; background: var(--board); border-radius: 32px; padding: 30px 30px 32px; box-shadow: 0 30px 60px -34px rgba(42, 42, 92, 0.3); }

        a { color: inherit; }

        /* ---------- Judul halaman ---------- */
        .page-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .page-head h1 { font-size: 26px; font-weight: 800; letter-spacing: -0.5px; line-height: 1.15; }
        .page-head p { margin-top: 6px; font-size: 13px; font-weight: 500; color: var(--muted); }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            height: 44px; padding: 0 22px; border-radius: 999px;
            background: var(--ink); color: #fff; text-decoration: none;
            font-size: 13.5px; font-weight: 700;
            box-shadow: 0 14px 26px -12px rgba(42, 42, 92, 0.6);
        }
        .btn-primary:hover { background: var(--ink-strong); }

        /* ---------- Banner pengingat ---------- */
        .banner {
            margin-top: 20px; padding: 12px 16px; border-radius: 16px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
            background: var(--danger-bg); color: #8f1b34;
            font-size: 13px; font-weight: 600;
        }
        .banner strong { font-weight: 800; }
        .banner.is-aman { background: #e9f6ef; color: #1f6b44; }
        .banner a {
            padding: 7px 14px; border-radius: 999px; background: #fff; color: var(--ink);
            font-size: 12px; font-weight: 700; text-decoration: none;
            box-shadow: 0 4px 10px -6px rgba(42, 42, 92, 0.35);
        }

        /* ---------- Grid utama ---------- */
        .grid { margin-top: 20px; display: grid; grid-template-columns: minmax(0, 1.9fr) minmax(0, 1fr); gap: 20px; align-items: start; }
        .col { display: flex; flex-direction: column; gap: 22px; min-width: 0; }

        .row-top { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 18px; }

        .section-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px; }
        .section-head h2 { font-size: 15px; font-weight: 800; letter-spacing: -0.2px; }
        .section-head .hint { display: block; margin-top: 2px; font-size: 11.5px; font-weight: 500; color: var(--muted); }
        .pill-link {
            padding: 6px 14px; border-radius: 999px; background: #fff; color: var(--ink);
            font-size: 11.5px; font-weight: 700; text-decoration: none; white-space: nowrap;
            box-shadow: 0 4px 10px -6px rgba(42, 42, 92, 0.35);
        }

        /* ---------- Kartu posisi bersih ---------- */
        .posisi {
            position: relative; overflow: hidden; color: #fff; border-radius: 26px; padding: 22px 22px 18px;
            min-height: 250px; display: flex; flex-direction: column; justify-content: space-between;
            background: linear-gradient(150deg, #2d2a62 0%, #4c3a78 45%, #7b4768 75%, #a45450 100%);
            box-shadow: 0 24px 40px -22px rgba(45, 42, 98, 0.7);
        }
        .posisi::after { content: ''; position: absolute; width: 230px; height: 230px; border-radius: 50%; right: -70px; bottom: -90px; background: rgba(255, 255, 255, 0.1); pointer-events: none; }
        .posisi > * { position: relative; z-index: 1; }
        .posisi .judul { font-size: 10.5px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: rgba(255, 255, 255, 0.78); }
        .posisi .rumus { margin-top: 3px; font-size: 11px; font-weight: 500; color: rgba(255, 255, 255, 0.65); }
        .posisi .nilai { margin-top: 8px; font-size: 30px; font-weight: 800; letter-spacing: -0.8px; }
        .posisi .pecahan { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px; }
        .posisi .pecahan small { display: block; font-size: 10px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; color: rgba(255, 255, 255, 0.7); }
        .posisi .pecahan b { display: block; margin-top: 3px; font-size: 14px; font-weight: 800; }
        .posisi .pecahan em { display: block; margin-top: 1px; font-style: normal; font-size: 10.5px; font-weight: 500; color: rgba(255, 255, 255, 0.65); }
        .posisi .pemilik { margin-top: 14px; font-size: 10.5px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: rgba(255, 255, 255, 0.78); }

        /* ---------- Kartu jatuh tempo terdekat ---------- */
        .mini-list { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .mini {
            display: flex; flex-direction: column; gap: 3px; padding: 16px; border-radius: 22px;
            background: var(--card); text-decoration: none; min-width: 0;
            box-shadow: 0 14px 26px -20px rgba(42, 42, 92, 0.45);
        }
        .mini:hover { background: #fff; }
        .mini .ikon { width: 34px; height: 34px; border-radius: 11px; background: var(--ink); color: #fff; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
        .mini b { font-size: 13px; font-weight: 800; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .mini span { font-size: 11.5px; font-weight: 500; color: var(--muted); }
        .mini strong { margin-top: 8px; font-size: 16px; font-weight: 800; }
        .kosong { padding: 18px; border-radius: 18px; background: var(--card); font-size: 12.5px; font-weight: 500; color: var(--muted); grid-column: 1 / -1; }

        /* ---------- Daftar perlu perhatian ---------- */
        .daftar { background: var(--card); border-radius: 24px; padding: 6px 18px; }
        .baris { display: flex; align-items: center; gap: 12px; padding: 14px 0; text-decoration: none; border-top: 1px solid rgba(42, 42, 92, 0.08); }
        .baris:first-child { border-top: 0; }
        .baris .ikon { color: var(--muted); flex-shrink: 0; display: flex; }
        .baris .info { flex: 1; min-width: 0; }
        .baris .info b { display: block; font-size: 13px; font-weight: 800; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .baris .info span { display: block; margin-top: 2px; font-size: 11.5px; font-weight: 500; color: var(--muted); }
        .baris .jenis { font-size: 10.5px; font-weight: 700; color: var(--muted); }
        .baris .nominal { font-size: 13px; font-weight: 800; white-space: nowrap; }
        .badge { padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .badge.merah { background: var(--danger-bg); color: #a3153f; }
        .badge.kuning { background: var(--warn-bg); color: var(--warn-ink); }
        .badge.netral { background: #e8e7e3; color: var(--muted); }
        .daftar .kosong { background: transparent; grid-column: auto; text-align: center; padding: 26px 8px; }

        /* ---------- Panel kanan ---------- */
        .panel { background: var(--card); border-radius: 28px; padding: 22px 20px; display: flex; flex-direction: column; gap: 24px; min-width: 0; }
        .panel h3 { font-size: 14.5px; font-weight: 800; letter-spacing: -0.2px; }
        .panel .sub { margin-top: 2px; font-size: 11.5px; font-weight: 500; color: var(--muted); }

        .dibayar-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
        .dibayar-head .nilai { margin-top: 6px; font-size: 26px; font-weight: 800; letter-spacing: -0.6px; }
        .chip { padding: 6px 12px; border-radius: 999px; background: #fff; font-size: 11.5px; font-weight: 700; white-space: nowrap; box-shadow: 0 4px 10px -6px rgba(42, 42, 92, 0.35); }

        .legenda { display: flex; flex-direction: column; gap: 4px; margin-top: 10px; font-size: 11.5px; font-weight: 600; }
        .legenda i { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; }
        .legenda .masuk i { background: var(--ink); }
        .legenda .keluar i { background: #d12f4d; }
        .legenda .keluar { color: #a3153f; }

        .grafik { width: 100%; height: auto; margin-top: 8px; display: block; }

        .status-item + .status-item { margin-top: 12px; }
        .status-item .baris-status { display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; }
        .status-item .baris-status span:last-child { color: var(--muted); font-weight: 600; }
        .track { margin-top: 6px; height: 7px; border-radius: 999px; background: rgba(42, 42, 92, 0.1); overflow: hidden; }
        .track div { height: 100%; border-radius: 999px; min-width: 0; }

        .total { margin-top: 16px; padding-top: 14px; border-top: 1px solid rgba(42, 42, 92, 0.1); display: grid; gap: 6px; font-size: 12px; font-weight: 600; color: var(--muted); }
        .total div { display: flex; justify-content: space-between; gap: 8px; }
        .total b { color: var(--ink); font-weight: 800; }

        .aktivitas { margin-top: 10px; }
        .aktivitas .item { padding: 10px 0; border-top: 1px solid rgba(42, 42, 92, 0.08); }
        .aktivitas .item:first-child { border-top: 0; }
        .aktivitas b { display: block; font-size: 12.5px; font-weight: 800; }
        .aktivitas span { display: block; margin-top: 2px; font-size: 11.5px; font-weight: 500; color: var(--muted); }
        .aktivitas .kosong { background: transparent; padding: 10px 0; }

        /* ---------- Toast pengingat setelah login ---------- */
        .toast {
            position: fixed; bottom: 20px; right: 20px; z-index: 50; max-width: 340px;
            display: flex; gap: 12px; align-items: flex-start; padding: 14px 16px; border-radius: 18px;
            background: #fff; box-shadow: 0 24px 48px -16px rgba(42, 42, 92, 0.4);
            border-left: 5px solid #d12f4d;
        }
        .toast strong { display: block; font-size: 13px; font-weight: 800; }
        .toast p { margin-top: 3px; font-size: 12px; font-weight: 500; line-height: 1.5; color: var(--muted); }
        .toast a { display: inline-block; margin-top: 6px; font-size: 12px; font-weight: 800; color: var(--accent); text-decoration: none; }
        .toast button { border: 0; background: none; font-size: 20px; line-height: 1; color: var(--muted); cursor: pointer; }

        @media (max-width: 1020px) {
            .grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 720px) {
            body { padding: 0; }
            .shell { border-radius: 0; padding: 12px; }
            .board { padding: 20px 16px 24px; border-radius: 24px; }
            .row-top { grid-template-columns: 1fr; }
            .baris { flex-wrap: wrap; row-gap: 8px; }
            .baris .info { flex: 1 1 calc(100% - 34px); }
            .baris .jenis { margin-left: 30px; }
            .baris .nominal { margin-left: auto; }
        }
    </style>
</head>
<body>
@php
    $rp = fn ($n) => ($n < 0 ? '-' : '').'Rp'.number_format(abs($n), 0, ',', '.');
    $namaPengguna = auth()->user()->name ?? 'Owner';
    $namaDepan = explode(' ', trim($namaPengguna))[0];
@endphp

@if (session('pengingat_login') && session('pengingat_login')['jumlah'] > 0)
    @php($pengingatLogin = session('pengingat_login'))
    <div class="toast" id="toastPengingat" role="alert">
        <div>
            <strong>Pengingat jatuh tempo</strong>
            <p>
                {{ $pengingatLogin['jumlah'] }} kuitansi perlu perhatian
                @if ($pengingatLogin['terlambat'] > 0)
                    ({{ $pengingatLogin['terlambat'] }} terlambat)
                @endif
                • {{ $rp($pengingatLogin['sisa']) }} belum diselesaikan.
            </p>
            <a href="{{ route('jatuh-tempo.index') }}">Lihat detail</a>
        </div>
        <button type="button" onclick="document.getElementById('toastPengingat').remove()" aria-label="Tutup pengingat">&times;</button>
    </div>
@endif

<div class="shell">
    @include('partials.topnav', ['aktifMenu' => 'dashboard', 'badgePengingat' => $perluPerhatianCount])

    <main class="board">
        <div class="page-head">
            <div>
                <h1>Dashboard</h1>
                <p>{{ $salam }}, Pak {{ $namaDepan }}. Kondisi utang-piutang Toko Sukses Abadi per {{ $tanggalAcuan->translatedFormat('d F Y') }}.</p>
            </div>
            <a href="{{ route('kuitansi.create') }}" class="btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah kuitansi
            </a>
        </div>

        @if ($perluPerhatianCount > 0)
            <div class="banner" role="status">
                <span><strong>Peringatan jatuh tempo</strong> · {{ $perluPerhatianCount }} tagihan perlu perhatian mulai H-{{ \App\Models\Kuitansi::HARI_PENGINGAT }} ({{ $terlambatCount }} terlambat).</span>
                <a href="{{ route('jatuh-tempo.index') }}">Lihat detail</a>
            </div>
        @else
            <div class="banner is-aman" role="status">
                <span><strong>Peringatan jatuh tempo</strong> · Belum ada tagihan yang terlambat atau jatuh tempo dalam {{ \App\Models\Kuitansi::HARI_PENGINGAT }} hari ke depan.</span>
                <a href="{{ route('jatuh-tempo.index') }}">Lihat semua tagihan</a>
            </div>
        @endif

        <div class="grid">
            <div class="col">
                <div class="row-top">
                    <section class="posisi" aria-labelledby="judul-posisi">
                        <div>
                            <div class="judul" id="judul-posisi">Posisi bersih toko</div>
                            <div class="rumus">Sisa piutang dikurangi sisa utang</div>
                            <div class="nilai">{{ $rp($posisiBersih) }}</div>
                            <div class="pecahan">
                                <div>
                                    <small>Sisa piutang</small>
                                    <b>{{ $rp($sisaPiutang) }}</b>
                                    <em>Belum dibayar pelanggan · {{ $jumlahPiutang }} kuitansi</em>
                                </div>
                                <div>
                                    <small>Sisa utang</small>
                                    <b>{{ $rp($sisaUtang) }}</b>
                                    <em>Belum dibayar toko · {{ $jumlahUtang }} kuitansi</em>
                                </div>
                            </div>
                        </div>
                        <div class="pemilik">{{ $namaPengguna }} · Owner</div>
                    </section>

                    <section aria-labelledby="judul-terdekat">
                        <div class="section-head">
                            <h2 id="judul-terdekat">Jatuh tempo terdekat</h2>
                            <a href="{{ route('jatuh-tempo.index') }}" class="pill-link">Lihat semua</a>
                        </div>
                        <div class="mini-list">
                            @forelse ($terdekat as $item)
                                <a href="{{ route('jatuh-tempo.index', ['selected' => $item->id]) }}" class="mini">
                                    <span class="ikon" aria-hidden="true">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    </span>
                                    <b>{{ $item->nama_pihak }}</b>
                                    <span>{{ $item->jenis === 'piutang' ? 'Piutang' : 'Utang' }} · {{ $item->badge }}</span>
                                    <strong>{{ $rp($item->sisa) }}</strong>
                                </a>
                            @empty
                                <div class="kosong">Belum ada tagihan yang akan jatuh tempo.</div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <section aria-labelledby="judul-perhatian">
                    <div class="section-head">
                        <div>
                            <h2 id="judul-perhatian">Perlu perhatian</h2>
                            <span class="hint">Terlambat atau jatuh tempo dalam {{ \App\Models\Kuitansi::HARI_PENGINGAT }} hari ke depan</span>
                        </div>
                        <a href="{{ route('jatuh-tempo.index') }}" class="pill-link">Lihat semua</a>
                    </div>
                    <div class="daftar">
                        @forelse ($pengingat->take(6) as $item)
                            <a href="{{ route('jatuh-tempo.index', ['selected' => $item->id]) }}" class="baris">
                                <span class="ikon" aria-hidden="true">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                </span>
                                <span class="info">
                                    <b>{{ $item->nama_pihak }}</b>
                                    <span>{{ $item->nomor_kuitansi }} · Tempo {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}</span>
                                </span>
                                <span class="jenis">{{ $item->jenis === 'piutang' ? 'Piutang' : 'Utang' }}</span>
                                <span class="nominal">{{ $rp($item->sisa) }}</span>
                                <span class="badge {{ $item->badge_kelas }}">{{ $item->badge }}</span>
                            </a>
                        @empty
                            <div class="kosong">Tidak ada tagihan yang memerlukan perhatian saat ini.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="panel" aria-label="Ringkasan pembayaran">
                <section aria-labelledby="judul-dibayar">
                    <div class="dibayar-head">
                        <div>
                            <h3 id="judul-dibayar">Dibayar bulan ini</h3>
                            <div class="sub">Total pembayaran yang dicatat</div>
                            <div class="nilai">{{ $rp($dibayarBulanIni) }}</div>
                        </div>
                        <span class="chip">{{ $tanggalAcuan->translatedFormat('M Y') }}</span>
                    </div>

                    <div class="legenda">
                        <span class="masuk"><i></i>Piutang masuk (dari pelanggan) {{ $rp($piutangMasukBulanIni) }}</span>
                        <span class="keluar"><i></i>Utang keluar (ke supplier) {{ $rp($utangKeluarBulanIni) }}</span>
                    </div>

                    <svg class="grafik" viewBox="0 0 300 120" role="img" aria-label="Grafik pembayaran 6 bulan terakhir: piutang masuk dan utang keluar">
                        <line x1="12" y1="96" x2="288" y2="96" stroke="rgba(42,42,92,0.15)" stroke-width="1"></line>
                        <path d="{{ $grafik['path_piutang'] }}" fill="none" stroke="#2a2a5c" stroke-width="2.4" stroke-linecap="round"></path>
                        <path d="{{ $grafik['path_utang'] }}" fill="none" stroke="#d12f4d" stroke-width="2.4" stroke-linecap="round"></path>
                        @foreach ($grafik['bulan'] as $b)
                            <text x="{{ $b['x'] }}" y="114" text-anchor="middle" font-size="9.5" font-weight="600" fill="#6b6b86">{{ $b['label'] }}</text>
                            <circle cx="{{ $b['x'] }}" cy="{{ $b['y_piutang'] }}" r="3" fill="#2a2a5c"><title>{{ $b['label_penuh'] }} · piutang masuk {{ $rp($b['piutang']) }}</title></circle>
                            <circle cx="{{ $b['x'] }}" cy="{{ $b['y_utang'] }}" r="3" fill="#d12f4d"><title>{{ $b['label_penuh'] }} · utang keluar {{ $rp($b['utang']) }}</title></circle>
                        @endforeach
                    </svg>
                </section>

                <section aria-labelledby="judul-status">
                    <div class="section-head" style="margin-bottom: 10px;">
                        <h3 id="judul-status">Status pembayaran</h3>
                        <a href="{{ route('rekapitulasi.index') }}" class="pill-link">Lihat rekap</a>
                    </div>
                    @foreach ($statusPembayaran as $st)
                        <div class="status-item">
                            <div class="baris-status"><span>{{ $st['label'] }}</span><span>{{ $st['count'] }} kuitansi</span></div>
                            <div class="track"><div style="width: {{ $st['pct'] }}%; background: {{ $st['color'] }};"></div></div>
                        </div>
                    @endforeach
                    <div class="total">
                        <div><span>Total nilai tercatat</span><b>{{ $rp($totalNilaiTercatat) }}</b></div>
                        <div><span>Sudah dibayar</span><b>{{ $rp($totalTerbayar) }}</b></div>
                        <div><span>Belum dibayar (sisa piutang + sisa utang)</span><b>{{ $rp($totalSisa) }}</b></div>
                    </div>
                </section>

                <section aria-labelledby="judul-aktivitas">
                    <div class="section-head" style="margin-bottom: 0;">
                        <h3 id="judul-aktivitas">Aktivitas terbaru</h3>
                        <a href="{{ route('arsip-kuitansi.index') }}" class="pill-link">Lihat arsip</a>
                    </div>
                    <div class="aktivitas">
                        @forelse ($aktivitasTerbaru as $a)
                            <div class="item"><b>{{ $a['judul'] }}</b><span>{{ $a['subjudul'] }}</span></div>
                        @empty
                            <div class="kosong">Belum ada aktivitas.</div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </main>
</div>

<script>
    // Toast pengingat hilang otomatis setelah 12 detik
    (function () {
        var toast = document.getElementById('toastPengingat');
        if (toast) { setTimeout(function () { toast.remove(); }, 12000); }
    })();
</script>
</body>
</html>
