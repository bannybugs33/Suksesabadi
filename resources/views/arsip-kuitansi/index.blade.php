<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Kuitansi - BukuTempo Toko Sukses Abadi</title>
    @include('partials.topnav-styles')
    @include('partials.page-styles')
    <style>
        .filter { margin-top: 20px; background: var(--card); border-radius: 26px; padding: 18px 20px; display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1fr) auto; gap: 14px; align-items: end; box-shadow: 0 14px 26px -22px rgba(42, 42, 92, 0.45); }
        .filter label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; }
        .filter input, .filter select {
            width: 100%; height: 46px; padding: 0 16px; border: 1.5px solid transparent; border-radius: 16px;
            background: var(--field); box-shadow: inset 0 2px 5px rgba(42, 42, 92, 0.07);
            font: inherit; font-size: 13.5px; font-weight: 600; color: var(--ink-strong); outline: none;
        }
        .filter input::placeholder { color: #a4a3b3; font-weight: 500; }
        .filter input:focus, .filter select:focus { background: #fff; border-color: var(--ink); box-shadow: 0 0 0 4px rgba(42, 42, 92, 0.1); }

        .daftar-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
        .daftar-head h2 { font-size: 16px; font-weight: 800; letter-spacing: -0.2px; }
        .daftar-head h2 small { margin-left: 6px; font-size: 12px; font-weight: 600; color: var(--muted); }

        .ikon-baris { width: 34px; color: var(--muted); }
        .tabel .angka { font-weight: 600; white-space: nowrap; }
        .tabel .nominal { font-weight: 800; }
        .tempo small { display: block; margin-top: 3px; font-size: 11.5px; font-weight: 700; }
        .tempo small.merah { color: #a3153f; }
        .tempo small.kuning { color: var(--warn-ink); }
        .tempo small.netral { color: var(--muted); font-weight: 500; }
        .lainnya { display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; border-radius: 50%; color: var(--muted); text-decoration: none; font-size: 16px; letter-spacing: 1px; }
        .lainnya:hover { background: #fff; color: var(--ink); }
        .tabel tr.baris-arsip:hover td { background: rgba(255, 255, 255, 0.6); }

        .pager { margin-top: 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; font-size: 12.5px; font-weight: 500; color: var(--muted); }
        .pager nav { display: flex; gap: 8px; }
        .pager a, .pager span.cur { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 8px; border-radius: 50%; background: #fff; color: var(--ink); font-size: 12.5px; font-weight: 800; text-decoration: none; box-shadow: 0 6px 14px -8px rgba(42, 42, 92, 0.4); }
        .pager span.cur { background: var(--ink); color: #fff; }

        @media (max-width: 860px) { .filter { grid-template-columns: 1fr 1fr; } .filter .cari { grid-column: 1 / -1; } }
        @media (max-width: 520px) { .filter { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
@php
    $rp = fn ($n) => 'Rp'.number_format((float) $n, 0, ',', '.');
@endphp

<div class="shell">
    @include('partials.topnav', ['aktifMenu' => 'arsip'])

    <main class="board">
        <div class="page-head">
            <div>
                <h1>Arsip Kuitansi</h1>
                <p>Cari, periksa, dan kelola seluruh bukti transaksi utang-piutang.</p>
            </div>
            <a href="{{ route('kuitansi.create') }}" class="btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah kuitansi
            </a>
        </div>

        @if (session('status'))
            <div class="alert ok" role="status">{{ session('status') }}</div>
        @endif

        <form method="GET" action="{{ route('arsip-kuitansi.index') }}" class="filter" role="search">
            <input type="hidden" name="urut" value="{{ $urut }}">
            <div class="cari">
                <label for="q">Cari</label>
                <input type="search" id="q" name="q" value="{{ $kata }}" maxlength="100" placeholder="Nama pelanggan/supplier atau nomor kuitansi" autocomplete="off">
            </div>
            <div>
                <label for="jenis">Jenis</label>
                <select id="jenis" name="jenis" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="piutang" @selected(request('jenis') === 'piutang')>Piutang</option>
                    <option value="utang" @selected(request('jenis') === 'utang')>Utang</option>
                </select>
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach (\App\Models\Kuitansi::LABEL_STATUS as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(request('status') === $nilai)>{{ $label }}</option>
                    @endforeach
                    <option value="dibatalkan" @selected(request('status') === 'dibatalkan')>Dibatalkan</option>
                </select>
            </div>
            <a href="{{ route('arsip-kuitansi.index') }}" class="btn-soft">Hapus filter</a>
            <button type="submit" class="sr-only">Cari</button>
        </form>

        <div class="panel-card">
            <div class="daftar-head">
                <h2>Daftar kuitansi <small>{{ $kuitansi->total() }} data</small></h2>
                <a href="{{ request()->fullUrlWithQuery(['urut' => $urut === 'terbaru' ? 'terlama' : 'terbaru', 'page' => null]) }}" class="btn-soft">
                    {{ $urut === 'terbaru' ? 'Terbaru lebih dulu ↓' : 'Terlama lebih dulu ↑' }}
                </a>
            </div>

            @if ($kuitansi->isEmpty())
                <div class="kosong" role="status">
                    <b>Data tidak ditemukan</b>
                    @if ($adaFilter)
                        Tidak ada kuitansi yang cocok dengan pencarian atau filter. Coba kata kunci lain atau hapus filter.
                    @else
                        Belum ada arsip kuitansi. Klik "Tambah kuitansi" untuk menyimpan yang pertama.
                    @endif
                </div>
            @else
                <div class="tabel-wrap">
                    <table class="tabel">
                        <thead>
                            <tr>
                                <th class="ikon-baris"><span class="sr-only">Dokumen</span></th>
                                <th>Pelanggan / Supplier</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Jatuh tempo</th>
                                <th class="r">Nominal</th>
                                <th><span class="sr-only">Detail</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kuitansi as $item)
                                <tr class="baris-arsip">
                                    <td class="ikon-baris" aria-hidden="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h12a1 1 0 0 1 1 1v18l-3-2-3 2-3-2-3 2V3a1 1 0 0 1 1-1z"></path><line x1="9" y1="8" x2="15" y2="8"></line><line x1="9" y1="12" x2="15" y2="12"></line></svg>
                                    </td>
                                    <td class="nama">
                                        <a href="{{ route('arsip-kuitansi.show', $item) }}"><b>{{ $item->nama_pihak }}</b></a>
                                        <span>{{ $item->nomor_kuitansi }}@if ($item->nomor_kuitansi_fisik) · No. fisik {{ $item->nomor_kuitansi_fisik }}@endif @if ($item->keterangan) · {{ \Illuminate\Support\Str::limit($item->keterangan, 60) }}@endif</span>
                                    </td>
                                    <td class="angka">{{ $item->tanggal_kuitansi->translatedFormat('d M Y') }}</td>
                                    <td><span class="badge {{ $item->jenis }}">{{ $item->jenis === 'piutang' ? 'Piutang' : 'Utang' }}</span></td>
                                    <td>
                                        @if ($item->dibatalkan_pada)
                                            <span class="badge dibatalkan">Dibatalkan</span>
                                        @else
                                            <span class="badge {{ $item->status_pembayaran }}">{{ \App\Models\Kuitansi::labelStatus($item->status_pembayaran) }}</span>
                                        @endif
                                    </td>
                                    <td class="angka tempo">
                                        {{ $item->tanggal_jatuh_tempo?->translatedFormat('d M Y') ?? '-' }}
                                        @if ($item->tempo_teks)
                                            <small class="{{ $item->tempo_kelas }}">{{ $item->tempo_teks }}</small>
                                        @endif
                                    </td>
                                    <td class="r nominal">{{ $rp($item->nominal_transaksi) }}</td>
                                    <td class="r"><a class="lainnya" href="{{ route('arsip-kuitansi.show', $item) }}" aria-label="Buka detail {{ $item->nomor_kuitansi }}" title="Buka detail">···</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pager">
                    <span>Menampilkan {{ $kuitansi->firstItem() }}–{{ $kuitansi->lastItem() }} dari {{ $kuitansi->total() }} kuitansi</span>
                    @if ($kuitansi->hasPages())
                        <nav aria-label="Halaman">
                            @if (! $kuitansi->onFirstPage())
                                <a href="{{ $kuitansi->previousPageUrl() }}" aria-label="Halaman sebelumnya">‹</a>
                            @endif
                            @foreach ($kuitansi->getUrlRange(max(1, $kuitansi->currentPage() - 2), min($kuitansi->lastPage(), $kuitansi->currentPage() + 2)) as $nomor => $url)
                                @if ($nomor === $kuitansi->currentPage())
                                    <span class="cur" aria-current="page">{{ $nomor }}</span>
                                @else
                                    <a href="{{ $url }}">{{ $nomor }}</a>
                                @endif
                            @endforeach
                            @if ($kuitansi->hasMorePages())
                                <a href="{{ $kuitansi->nextPageUrl() }}" aria-label="Halaman berikutnya">›</a>
                            @endif
                        </nav>
                    @endif
                </div>
            @endif
        </div>
    </main>
</div>
</body>
</html>