<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Kuitansi | BukuTempo</title>
    @include('arsip-kuitansi._styles')
    @include('arsip-kuitansi._page')
</head>
<body>
@include('arsip-kuitansi._sidebar')
<main class="main-content">
    <div class="page-top">
        <div>
            <h1 class="page-title">Arsip Kuitansi</h1>
            <p class="page-subtitle">Cari, periksa, dan kelola seluruh bukti transaksi utang-piutang toko.</p>
        </div>
        <a href="{{ route('dashboard', ['action' => 'tambah']) }}" class="btn btn-primary">+ Tambah kuitansi</a>
    </div>

    @if (session('status'))
        <div class="alert-ok">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('arsip-kuitansi.index') }}" class="card">
        <input type="text" name="q" class="field" value="{{ request('q') }}" placeholder="Cari nama pelanggan/supplier atau nomor kuitansi">
        <div class="filter-row">
            <span class="filter-label">FILTER</span>
            <select name="jenis" class="field-select">
                <option value="">Semua jenis</option>
                <option value="piutang" @selected(request('jenis') === 'piutang')>Piutang</option>
                <option value="utang" @selected(request('jenis') === 'utang')>Utang</option>
            </select>
            <select name="status" class="field-select">
                <option value="">Semua status</option>
                @foreach (['belum_lunas' => 'Belum lunas', 'dp' => 'DP', 'cicilan' => 'Cicilan', 'lunas' => 'Lunas'] as $nilai => $label)
                    <option value="{{ $nilai }}" @selected(request('status') === $nilai)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="dari" class="field-select" value="{{ request('dari') }}" title="Dari tanggal">
            <input type="date" name="sampai" class="field-select" value="{{ request('sampai') }}" title="Sampai tanggal">
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="{{ route('arsip-kuitansi.index') }}" class="link-red">Hapus filter</a>
        </div>
    </form>

    @if ($kuitansi->isEmpty())
        <div class="card empty">
            <b>Tidak ada kuitansi</b>
            Tidak ada data kuitansi yang sesuai dengan pencarian. Coba ubah kata kunci atau reset filter.
        </div>
    @else
        <div class="card">
            <h3>Daftar kuitansi <span style="font-weight:400;color:#777;font-size:11px">{{ $kuitansi->total() }} data</span></h3>
            <table>
                <thead>
                    <tr>
                        <th>NO. KUITANSI</th><th>TANGGAL</th><th>PELANGGAN / SUPPLIER</th><th>JENIS</th>
                        <th style="text-align:right">NOMINAL</th><th>STATUS</th><th>JATUH TEMPO</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($kuitansi as $item)
                    @php
                        $terlambat = $item->tanggal_jatuh_tempo && $item->status_pembayaran !== 'lunas' && $item->tanggal_jatuh_tempo->isPast() && ! $item->tanggal_jatuh_tempo->isToday();
                    @endphp
                    <tr class="row-link" onclick="window.location='{{ route('arsip-kuitansi.show', $item) }}'">
                        <td><a class="link-red" href="{{ route('arsip-kuitansi.show', $item) }}">{{ $item->nomor_kuitansi }}</a></td>
                        <td>{{ $item->tanggal_kuitansi->translatedFormat('d M Y') }}</td>
                        <td>{{ $item->nama_pihak }}<small>{{ $item->keterangan }}</small></td>
                        <td style="color:#777">{{ ucfirst($item->jenis) }}</td>
                        <td class="num">Rp{{ number_format($item->nominal_transaksi, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $item->status_pembayaran }}">{{ ucfirst(str_replace('_', ' ', $item->status_pembayaran)) }}</span></td>
                        <td>
                            {{ $item->tanggal_jatuh_tempo?->translatedFormat('d M Y') ?? '-' }}
                            @if ($terlambat)<span class="late">Lewat tempo</span>@endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pager">
                <span>Menampilkan {{ $kuitansi->firstItem() }}-{{ $kuitansi->lastItem() }} dari {{ $kuitansi->total() }} kuitansi</span>
                <span>
                    @if ($kuitansi->onFirstPage()) @else <a href="{{ $kuitansi->previousPageUrl() }}">‹</a> @endif
                    @foreach ($kuitansi->getUrlRange(1, $kuitansi->lastPage()) as $nomor => $url)
                        @if ($nomor === $kuitansi->currentPage())<span class="cur">{{ $nomor }}</span>@else<a href="{{ $url }}">{{ $nomor }}</a>@endif
                    @endforeach
                    @if ($kuitansi->hasMorePages())<a href="{{ $kuitansi->nextPageUrl() }}">›</a>@endif
                </span>
            </div>
        </div>
    @endif
</main>
</body>
</html>
