<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kuitansi {{ $kuitansi->nomor_kuitansi }} | BukuTempo</title>
    @include('arsip-kuitansi._styles')
    @include('arsip-kuitansi._page')
</head>
<body>
@include('arsip-kuitansi._sidebar')
@php
    $rp = fn ($angka) => 'Rp'.number_format($angka, 0, ',', '.');
    $bukti = $kuitansi->bukti_info;
    $lunas = $kuitansi->status_pembayaran === 'lunas' || $sisa <= 0;
@endphp
<main class="main-content">
    <a href="{{ route('arsip-kuitansi.index') }}" class="back">← Kembali ke Arsip Kuitansi</a>
    <div class="page-top">
        <div>
            <h1 class="page-title">Detail Kuitansi {{ $kuitansi->nomor_kuitansi }}</h1>
            <p class="page-subtitle">Dibuat {{ $kuitansi->tanggal_kuitansi->translatedFormat('d M Y') }} • Terakhir diperbarui {{ $kuitansi->updated_at->translatedFormat('d M Y, H.i') }}</p>
        </div>
        @if ($bukti['has_bukti'])
            <a href="{{ $bukti['bukti_kuitansi_url'] }}" download class="btn">⬇ Unduh bukti</a>
        @endif
    </div>

    <div class="stats">
        <div class="card">
            <div>
                <small>STATUS PEMBAYARAN TERBARU</small>
                <span class="badge {{ $kuitansi->status_pembayaran }}">{{ ucfirst(str_replace('_', ' ', $kuitansi->status_pembayaran)) }}</span>
                <span style="font-size:11px;color:#777">Terbayar {{ $rp($terbayar) }} dari {{ $rp($kuitansi->nominal_transaksi) }}</span>
            </div>
        </div>
        <div class="card">
            <div><small>JATUH TEMPO</small><b style="color:#c93548">{{ $kuitansi->tanggal_jatuh_tempo?->translatedFormat('d M Y') ?? '-' }}</b></div>
        </div>
    </div>

    <div class="cols">
        <div>
            <div class="card">
                <h3>Rincian transaksi</h3>
                <div class="kv"><span>Nomor kuitansi</span><b>{{ $kuitansi->nomor_kuitansi }}</b></div>
                <div class="kv"><span>Tanggal kuitansi</span><b>{{ $kuitansi->tanggal_kuitansi->translatedFormat('d F Y') }}</b></div>
                <div class="kv"><span>{{ $kuitansi->jenis === 'piutang' ? 'Pelanggan' : 'Supplier' }}</span><b>{{ $kuitansi->nama_pihak }}</b></div>
                <div class="kv"><span>Jenis kuitansi</span><b>{{ ucfirst($kuitansi->jenis) }}</b></div>
                <div class="kv"><span>Nominal transaksi</span><b>{{ $rp($kuitansi->nominal_transaksi) }}</b></div>
                <div class="kv"><span>Status saat ini</span><b>{{ ucfirst(str_replace('_', ' ', $kuitansi->status_pembayaran)) }}</b></div>
                <div class="kv"><span>Tanggal jatuh tempo</span><b>{{ $kuitansi->tanggal_jatuh_tempo?->translatedFormat('d F Y') ?? '-' }}</b></div>
                @if ($kuitansi->keterangan)
                    <div class="note"><span>Catatan transaksi</span>{{ $kuitansi->keterangan }}</div>
                @endif
            </div>

            <div class="card">
                <h3>Riwayat pembayaran <span style="font-weight:400;color:#c93548;font-size:11px">{{ $kuitansi->pembayaran->count() }} transaksi</span></h3>
                @forelse ($kuitansi->pembayaran as $bayar)
                    <div class="pay">
                        <div class="ck">✓</div>
                        <div>
                            <b>{{ $bayar->metode_pembayaran ?? 'Pembayaran' }}{{ $loop->last ? ' - pembayaran awal' : '' }}</b>
                            <small>{{ $bayar->tanggal_pembayaran->translatedFormat('d M Y') }}@if ($bayar->dicatat_oleh) • Dicatat {{ $bayar->dicatat_oleh }}@endif</small>
                        </div>
                        <b>+{{ $rp($bayar->nominal) }}</b>
                    </div>
                @empty
                    <p class="hlp">Belum ada pembayaran.</p>
                @endforelse
                <div class="sisa"><span>Sisa {{ $kuitansi->jenis === 'piutang' ? 'piutang' : 'utang' }}</span><b>{{ $rp($sisa) }}</b></div>
            </div>
        </div>

        <div>
            <div class="card">
                <h3>Foto kuitansi @if ($bukti['has_bukti'])<a href="{{ $bukti['bukti_kuitansi_url'] }}" target="_blank" class="link-red" style="font-weight:400">Buka penuh</a>@endif</h3>
                @if ($bukti['has_bukti'])
                    <img class="photo" src="{{ $bukti['bukti_kuitansi_url'] }}" alt="Foto kuitansi {{ $kuitansi->nomor_kuitansi }}" onerror="this.onerror=null;this.src='{{ $bukti['fallback_bukti_url'] }}'">
                    <p class="hlp">{{ $bukti['bukti_kuitansi_name'] }} • {{ $bukti['bukti_kuitansi_meta'] }}</p>
                @else
                    <p class="hlp">Belum ada foto kuitansi untuk transaksi ini.</p>
                @endif
            </div>

            @unless ($lunas)
            <form method="POST" action="{{ route('pembayaran.store', $kuitansi) }}" class="card">
                @csrf
                <input type="hidden" name="redirect_to" value="arsip">
                <h3>Perbarui status pembayaran</h3>
                <div class="fld">
                    <label for="nominal">Nominal pembayaran tambahan *</label>
                    <input id="nominal" name="nominal" type="number" min="1" max="{{ (int) $sisa }}" class="field" value="{{ old('nominal') }}" placeholder="Rp 0" required>
                    <div class="hlp">Nominal ini akan ditambahkan ke pembayaran sebelumnya.</div>
                    @error('nominal')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="fld">
                    <label for="metode">Metode pembayaran *</label>
                    <select id="metode" name="metode_pembayaran" class="field">
                        @foreach (['Tunai', 'Transfer bank'] as $metode)
                            <option value="{{ $metode }}" @selected(old('metode_pembayaran') === $metode)>{{ $metode }}</option>
                        @endforeach
                    </select>
                    @error('metode_pembayaran')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="fld">
                    <label for="tanggal">Tanggal pembayaran *</label>
                    <input id="tanggal" name="tanggal_pembayaran" type="date" class="field" max="{{ now()->toDateString() }}" value="{{ old('tanggal_pembayaran', now()->toDateString()) }}" required>
                    @error('tanggal_pembayaran')<div class="err">{{ $message }}</div>@enderror
                </div>
                <div class="fld">
                    <label for="catatan">Catatan</label>
                    <textarea id="catatan" name="catatan" rows="3" class="field" maxlength="255">{{ old('catatan') }}</textarea>
                </div>
                <div class="sum" id="ringkasan"
                     data-nominal="{{ (float) $kuitansi->nominal_transaksi }}" data-terbayar="{{ $terbayar }}"></div>
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
            </form>
            @endunless
        </div>
    </div>
</main>

@unless ($lunas)
<script>
    (function () {
        const rp = (n) => 'Rp' + Math.round(n).toLocaleString('id-ID');
        const box = document.getElementById('ringkasan');
        const input = document.getElementById('nominal');
        const nominal = parseFloat(box.dataset.nominal);
        const terbayar = parseFloat(box.dataset.terbayar);
        function render() {
            const tambahan = parseFloat(input.value) || 0;
            const total = terbayar + tambahan;
            box.innerHTML =
                '<div style="color:#c93548;font-weight:600;margin-bottom:4px">Ringkasan setelah disimpan</div>' +
                '<div class="kv"><span>Nominal transaksi</span><span>' + rp(nominal) + '</span></div>' +
                '<div class="kv"><span>Sudah dibayar</span><span>' + rp(terbayar) + '</span></div>' +
                '<div class="kv"><span>Pembayaran tambahan</span><span>' + rp(tambahan) + '</span></div>' +
                '<div class="kv"><b>Total terbayar</b><b>' + rp(total) + '</b></div>' +
                '<div class="kv" style="color:#c93548"><b>Sisa pembayaran</b><b>' + rp(Math.max(0, nominal - total)) + '</b></div>';
        }
        input.addEventListener('input', render);
        render();
    })();
</script>
@endunless
</body>
</html>
