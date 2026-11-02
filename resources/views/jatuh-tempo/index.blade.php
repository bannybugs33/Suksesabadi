<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jatuh Tempo - BukuTempo Toko Sukses Abadi</title>
    @include('partials.topnav-styles')
    @include('partials.page-styles')
    <style>
        .kartu-ringkas { margin-top: 20px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        .ringkas { background: var(--card); border-radius: 22px; padding: 18px 18px 16px; box-shadow: 0 14px 26px -22px rgba(42, 42, 92, 0.45); }
        .ringkas small { display: block; font-size: 12px; font-weight: 600; color: var(--muted); }
        .ringkas b { display: block; margin-top: 8px; font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
        .ringkas b.merah { color: #a3153f; }
        .ringkas span { display: block; margin-top: 4px; font-size: 11.5px; font-weight: 500; color: var(--muted); }

        .tabs { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .tab { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 999px; background: #fff; color: var(--ink); font-size: 12.5px; font-weight: 700; text-decoration: none; box-shadow: 0 6px 14px -8px rgba(42, 42, 92, 0.4); }
        .tab em { font-style: normal; font-weight: 800; }
        .tab.aktif { background: var(--ink); color: #fff; }
        .petunjuk { margin: 12px 2px 16px; font-size: 12px; font-weight: 500; color: var(--muted); }

        .tabel .angka { font-weight: 600; }
        .tabel .sisa { color: #a3153f; font-weight: 800; }
        .tabel tr.dipilih td { background: rgba(42, 42, 92, 0.05); }

        /* ---------- Modal Bayar ---------- */
        .modal { position: fixed; inset: 0; z-index: 60; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(42, 42, 92, 0.45); }
        .modal[hidden] { display: none; }
        .modal-card { width: 100%; max-width: 440px; max-height: 94vh; overflow-y: auto; background: #fdfdfc; border-radius: 28px; padding: 26px 24px 22px; box-shadow: 0 40px 80px -30px rgba(20, 20, 60, 0.6); }
        .modal-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
        .modal-head h2 { font-size: 18px; font-weight: 800; letter-spacing: -0.3px; }
        .modal-head p { margin-top: 4px; font-size: 12.5px; font-weight: 500; color: var(--muted); }
        .tutup { width: 34px; height: 34px; border: 0; border-radius: 50%; background: #fff; color: var(--ink); font-size: 18px; line-height: 1; cursor: pointer; box-shadow: 0 6px 14px -8px rgba(42, 42, 92, 0.4); flex-shrink: 0; }
        .modal form { margin-top: 18px; display: grid; gap: 14px; }
        .dua { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; }
        .modal input, .modal select {
            width: 100%; height: 46px; padding: 0 14px; border: 1.5px solid transparent; border-radius: 14px;
            background: var(--field); box-shadow: inset 0 2px 5px rgba(42, 42, 92, 0.07);
            font: inherit; font-size: 14px; font-weight: 600; color: var(--ink-strong); outline: none;
        }
        .modal input:focus, .modal select:focus { background: #fff; border-color: var(--ink); box-shadow: 0 0 0 4px rgba(42, 42, 92, 0.1); }
        .info-sisa { padding: 12px 14px; border-radius: 14px; background: var(--field); font-size: 12.5px; font-weight: 700; }
        .galat { padding: 12px 14px; border-radius: 14px; background: var(--danger-bg); color: #8f1b34; font-size: 12.5px; font-weight: 700; }
        .aksi { display: flex; justify-content: flex-end; gap: 10px; margin-top: 4px; }

        @media (max-width: 980px) { .kartu-ringkas { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 560px) { .kartu-ringkas { grid-template-columns: 1fr; } .dua { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
@php
    $rp = fn ($n) => ($n < 0 ? '-' : '').'Rp'.number_format(abs($n), 0, ',', '.');
    $tglAcuan = request()->only('tanggal_acuan');
    $bukaModalId = ($errors->any() && old('_kuitansi')) ? (int) old('_kuitansi') : null;
    $hari = \App\Models\Kuitansi::HARI_PENGINGAT;
@endphp

<div class="shell">
    @include('partials.topnav', ['aktifMenu' => 'jatuh-tempo', 'badgePengingat' => $jumlah['semua']])

    <main class="board">
        <div class="page-head">
            <div>
                <h1>Jatuh Tempo</h1>
                <p>Pantau tagihan yang perlu perhatian, rincian transaksi, dan bukti kuitansinya.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="alert ok" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any() && ! $bukaModalId)
            <div class="alert err" role="alert">{{ $errors->first() }}</div>
        @endif

        <div class="kartu-ringkas">
            <div class="ringkas">
                <small>Sisa perlu diselesaikan</small>
                <b class="merah">{{ $rp($totalSisa) }}</b>
                <span>Total sisa dari {{ $jumlah['semua'] }} kuitansi</span>
            </div>
            <div class="ringkas">
                <small>Piutang pelanggan</small>
                <b>{{ $rp($sisaPiutang) }}</b>
                <span>Berstatus perlu perhatian</span>
            </div>
            <div class="ringkas">
                <small>Utang ke supplier</small>
                <b>{{ $rp($sisaUtang) }}</b>
                <span>Harus dibayar toko</span>
            </div>
            <div class="ringkas">
                <small>Perlu perhatian</small>
                <b>{{ $jumlah['semua'] }} kuitansi</b>
                <span>{{ $jumlah['terlambat'] }} terlambat · {{ $jumlah['hari-ini'] }} hari ini · {{ $jumlah['h-7'] }} H-{{ $hari }}</span>
            </div>
        </div>

        <div class="panel-card">
            <nav class="tabs" aria-label="Kategori pengingat">
                @foreach (['semua' => 'Semua', 'h-7' => 'H-'.$hari, 'hari-ini' => 'Hari ini', 'terlambat' => 'Terlambat'] as $kunci => $label)
                    <a href="{{ route('jatuh-tempo.index', array_merge($tglAcuan, ['tab' => $kunci])) }}" @class(['tab', 'aktif' => $tab === $kunci]) @if ($tab === $kunci) aria-current="page" @endif>
                        {{ $label }} <em>{{ $jumlah[$kunci] }}</em>
                    </a>
                @endforeach
            </nav>
            <p class="petunjuk">Pengingat aktif mulai H-{{ $hari }} sebelum jatuh tempo sampai tagihan lunas. Hanya tagihan yang masih bersisa yang tampil.</p>

            @if ($daftar->isEmpty())
                <div class="kosong">
                    @if ($jumlah['semua'] === 0)
                        <b>Tidak ada tagihan yang perlu perhatian</b>
                        Tagihan akan muncul di sini mulai H-{{ $hari }} sebelum jatuh tempo.
                    @else
                        <b>Tidak ada tagihan pada kategori ini</b>
                        Pilih kategori lain untuk melihat tagihan yang perlu perhatian.
                    @endif
                </div>
            @else
                <div class="tabel-wrap">
                    <table class="tabel">
                        <thead>
                            <tr>
                                <th>Pelanggan / Supplier</th>
                                <th>Jenis</th>
                                <th>Jatuh tempo</th>
                                <th class="r">Nominal awal</th>
                                <th class="r">Terbayar</th>
                                <th class="r">Sisa</th>
                                <th>Pengingat</th>
                                <th><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftar as $item)
                                <tr @class(['dipilih' => $dipilihId === (int) $item->id])>
                                    <td class="nama">
                                        <a href="{{ route('arsip-kuitansi.show', $item->id) }}" title="Buka rincian transaksi dan bukti kuitansi">
                                            <b>{{ $item->nama_pihak }}</b>
                                        </a>
                                        <span>{{ $item->nomor_kuitansi }}</span>
                                    </td>
                                    <td><span class="badge {{ $item->jenis }}">{{ $item->jenis === 'piutang' ? 'Piutang' : 'Utang' }}</span></td>
                                    <td class="angka">{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}</td>
                                    <td class="r angka">{{ $rp($item->nominal_transaksi) }}</td>
                                    <td class="r angka">{{ $rp($item->terbayar) }}</td>
                                    <td class="r sisa">{{ $rp($item->sisa) }}</td>
                                    <td><span class="badge {{ $item->badge_kelas }}">{{ $item->badge }}</span></td>
                                    <td class="r">
                                        <button type="button" class="btn-soft" data-bayar
                                                data-id="{{ $item->id }}"
                                                data-action="{{ route('pembayaran.store', $item->id) }}"
                                                data-nomor="{{ $item->nomor_kuitansi }}"
                                                data-nama="{{ $item->nama_pihak }}"
                                                data-sisa="{{ (int) round($item->sisa) }}">Bayar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </main>
</div>

{{-- Modal catat pembayaran: memakai fitur pembayaran yang sudah ada (pembayaran.store) --}}
<div class="modal" id="modalBayar" hidden role="dialog" aria-modal="true" aria-labelledby="modalBayarJudul">
    <div class="modal-card">
        <div class="modal-head">
            <div>
                <h2 id="modalBayarJudul">Tambah pembayaran</h2>
                <p id="modalBayarSub"></p>
            </div>
            <button type="button" class="tutup" data-tutup aria-label="Tutup">&times;</button>
        </div>

        <form method="POST" action="" id="formBayar">
            @csrf
            <input type="hidden" name="_kuitansi" id="fKuitansi" value="{{ old('_kuitansi') }}">

            @if ($bukaModalId)
                <div class="galat" role="alert">{{ $errors->first() }}</div>
            @endif

            <div class="dua">
                <div>
                    <label for="fTanggal">Tanggal pembayaran</label>
                    <input type="date" id="fTanggal" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $hariIni) }}" max="{{ $hariIni }}" required>
                </div>
                <div>
                    <label for="fNominal">Jumlah (Rp)</label>
                    <input type="number" id="fNominal" name="nominal" min="1" step="1" inputmode="numeric" value="{{ old('nominal') }}" required>
                </div>
            </div>

            <div>
                <label for="fMetode">Metode</label>
                <select id="fMetode" name="metode_pembayaran">
                    <option value="Tunai" @selected(old('metode_pembayaran') === 'Tunai')>Tunai</option>
                    <option value="Transfer bank" @selected(old('metode_pembayaran', 'Transfer bank') === 'Transfer bank')>Transfer</option>
                </select>
            </div>

            <div>
                <label for="fCatatan">Catatan</label>
                <input type="text" id="fCatatan" name="catatan" maxlength="255" value="{{ old('catatan') }}">
            </div>

            <div class="info-sisa" id="infoSisa" aria-live="polite"></div>

            <div class="aksi">
                <button type="button" class="btn-soft" data-tutup>Batal</button>
                <button type="submit" class="btn-primary">Simpan pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var modal = document.getElementById('modalBayar');
        var form = document.getElementById('formBayar');
        var nominal = document.getElementById('fNominal');
        var info = document.getElementById('infoSisa');
        var sub = document.getElementById('modalBayarSub');
        var idField = document.getElementById('fKuitansi');
        var sisaSaatIni = 0;
        var pemicu = null;

        function rp(n) { return 'Rp' + Math.round(n).toLocaleString('id-ID'); }

        function perbaruiInfo() {
            var bayar = parseFloat(nominal.value) || 0;
            var sesudah = Math.max(0, sisaSaatIni - bayar);
            info.textContent = 'Sisa saat ini ' + rp(sisaSaatIni) + '. Setelah disimpan: ' + rp(sesudah) + '.';
        }

        function buka(tombol) {
            pemicu = tombol;
            sisaSaatIni = parseFloat(tombol.dataset.sisa) || 0;
            form.action = tombol.dataset.action;
            idField.value = tombol.dataset.id;
            sub.textContent = 'Catat pembayaran baru untuk ' + tombol.dataset.nomor + ' · ' + tombol.dataset.nama;
            nominal.max = sisaSaatIni;
            perbaruiInfo();
            modal.hidden = false;
            nominal.focus();
        }

        function tutup() {
            modal.hidden = true;
            if (pemicu) { pemicu.focus(); }
        }

        document.querySelectorAll('[data-bayar]').forEach(function (b) {
            b.addEventListener('click', function () { buka(b); });
        });
        document.querySelectorAll('[data-tutup]').forEach(function (b) { b.addEventListener('click', tutup); });
        modal.addEventListener('click', function (e) { if (e.target === modal) { tutup(); } });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) { tutup(); } });
        nominal.addEventListener('input', perbaruiInfo);

        // Setelah gagal validasi, buka kembali modal untuk tagihan yang sama dengan isian sebelumnya.
        var bukaId = {{ $bukaModalId ?? 'null' }};
        if (bukaId) {
            var tombol = document.querySelector('[data-bayar][data-id="' + bukaId + '"]');
            if (tombol) { buka(tombol); }
        }
    })();
</script>
</body>
</html>
