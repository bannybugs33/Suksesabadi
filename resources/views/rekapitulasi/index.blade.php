<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi - BukuTempo Toko Sukses Abadi</title>
    @include('partials.topnav-styles')
    @include('partials.page-styles')
    <style>
        .filter { margin-top: 20px; background: var(--card); border-radius: 26px; padding: 18px 20px; display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto; gap: 14px; align-items: end; box-shadow: 0 14px 26px -22px rgba(42, 42, 92, 0.45); }
        .filter label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; }
        .filter select {
            width: 100%; height: 46px; padding: 0 16px; border: 1.5px solid transparent; border-radius: 16px;
            background: var(--field); box-shadow: inset 0 2px 5px rgba(42, 42, 92, 0.07);
            font: inherit; font-size: 13.5px; font-weight: 600; color: var(--ink-strong); outline: none;
        }
        .filter select:focus { background: #fff; border-color: var(--ink); box-shadow: 0 0 0 4px rgba(42, 42, 92, 0.1); }
        .filter .data-per { height: 46px; display: flex; align-items: center; font-size: 12.5px; font-weight: 500; color: var(--muted); white-space: nowrap; }

        .kartu-ringkas { margin-top: 20px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        .ringkas { background: var(--card); border-radius: 22px; padding: 22px 22px 20px; box-shadow: 0 14px 26px -22px rgba(42, 42, 92, 0.45); }
        .ringkas small { display: block; font-size: 12px; font-weight: 600; color: var(--muted); }
        .ringkas b { display: block; margin-top: 12px; font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
        .ringkas span { display: block; margin-top: 8px; font-size: 11.5px; font-weight: 500; color: var(--muted); }
        .ringkas.utama { background: var(--ink); box-shadow: 0 22px 36px -22px rgba(42, 42, 92, 0.7); }
        .ringkas.utama small, .ringkas.utama span { color: rgba(255, 255, 255, 0.8); }
        .ringkas.utama b { color: #fff; }

        .panel-card h2 { margin-bottom: 14px; font-size: 16px; font-weight: 800; letter-spacing: -0.2px; }
        .tabel td { font-weight: 500; }
        .tabel .angka { white-space: nowrap; }
        .tabel .nominal { font-weight: 700; }
        .tabel tr.baris-rekap:hover td { background: rgba(255, 255, 255, 0.6); }
        .badge.penerimaan { background: #dff3e8; color: #1f6b44; }
        .badge.pengeluaran { background: var(--danger-bg); color: #a3153f; }

        @media (max-width: 980px) { .kartu-ringkas { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 720px) { .filter { grid-template-columns: 1fr 1fr; } .filter .data-per { grid-column: 1 / -1; height: auto; } }
        @media (max-width: 520px) { .kartu-ringkas, .filter { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
@php
    $rp = fn ($n) => ($n < 0 ? '-' : '').'Rp'.number_format(abs($n), 0, ',', '.');
@endphp

<div class="shell">
    @include('partials.topnav', ['aktifMenu' => 'rekapitulasi'])

    <main class="board">
        <div class="page-head">
            <div>
                <h1>Rekapitulasi</h1>
                <p>Ringkasan penerimaan dan pengeluaran berdasarkan tanggal pembayaran.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('rekapitulasi.index') }}" class="filter" role="search">
            <div>
                <label for="periode">Periode tanggal pembayaran</label>
                <select id="periode" name="periode" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach ($daftarPeriode as $nilai => $label)
                        <option value="{{ $nilai }}" @selected($periode === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="arah">Arah</label>
                <select id="arah" name="arah" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="penerimaan" @selected($arah === 'penerimaan')>Penerimaan</option>
                    <option value="pengeluaran" @selected($arah === 'pengeluaran')>Pengeluaran</option>
                </select>
            </div>
            <div class="data-per">Data per {{ $dataPer->translatedFormat('j M Y') }}</div>
            <button type="submit" class="sr-only">Terapkan</button>
        </form>

        <div class="kartu-ringkas">
            <div class="ringkas">
                <small>Total penerimaan</small>
                <b>{{ $rp($totalPenerimaan) }}</b>
                <span>Dari pembayaran pelanggan</span>
            </div>
            <div class="ringkas">
                <small>Total pengeluaran</small>
                <b>{{ $rp($totalPengeluaran) }}</b>
                <span>Pembayaran ke supplier</span>
            </div>
            <div class="ringkas utama">
                <small>Posisi bersih</small>
                <b>{{ $rp($posisiBersih) }}</b>
                <span>Penerimaan - pengeluaran</span>
            </div>
            <div class="ringkas">
                <small>Jumlah pembayaran</small>
                <b>{{ $jumlahPembayaran }}</b>
                <span>Transaksi aktif</span>
            </div>
        </div>

        <div class="panel-card">
            <h2>Rincian pembayaran</h2>

            @if ($pembayaran->isEmpty())
                <div class="kosong" role="status">
                    <b>Belum ada pembayaran</b>
                    @if ($adaFilter)
                        Tidak ada pembayaran pada periode atau arah yang dipilih. Coba pilih "Semua".
                    @else
                        Pembayaran yang dicatat pada kuitansi akan muncul di sini.
                    @endif
                </div>
            @else
                <div class="tabel-wrap">
                    <table class="tabel">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Pelanggan / Supplier</th>
                                <th>No. kuitansi</th>
                                <th>Arah</th>
                                <th>Metode</th>
                                <th class="r">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembayaran as $baris)
                                <tr class="baris-rekap">
                                    <td class="angka">{{ $baris->tanggal->translatedFormat('j M Y') }}</td>
                                    <td class="nama"><a href="{{ route('arsip-kuitansi.show', $baris->kuitansi_id) }}"><b>{{ $baris->nama_pihak }}</b></a></td>
                                    <td class="angka">{{ $baris->nomor_kuitansi }}</td>
                                    <td><span class="badge {{ $baris->arah }}">{{ $baris->arah === 'penerimaan' ? 'Penerimaan' : 'Pengeluaran' }}</span></td>
                                    <td class="angka">{{ $baris->metode }}</td>
                                    <td class="r nominal">{{ $rp($baris->nominal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>