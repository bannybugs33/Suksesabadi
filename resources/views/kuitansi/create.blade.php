<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Kuitansi Baru - BukuTempo Toko Sukses Abadi</title>
    @include('partials.topnav-styles')
    @include('partials.page-styles')
    <style>
        .steps { margin-top: 20px; display: flex; flex-wrap: wrap; gap: 10px; padding: 12px 16px; border-radius: 20px; background: var(--card); box-shadow: 0 14px 26px -22px rgba(42, 42, 92, 0.45); }
        .step { padding: 6px 14px; border-radius: 999px; background: #e8e7ef; color: var(--ink); font-size: 11.5px; font-weight: 700; }
        .step.selesai { background: #dff3e8; color: #1f6b44; }

        .tata { margin-top: 18px; display: grid; grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr); gap: 18px; align-items: start; }
        .kolom { display: flex; flex-direction: column; gap: 18px; min-width: 0; }
        .kartu { background: var(--card); border-radius: 26px; padding: 22px 22px 20px; box-shadow: 0 14px 26px -22px rgba(42, 42, 92, 0.45); }
        .kartu h2 { font-size: 16px; font-weight: 800; letter-spacing: -0.2px; margin-bottom: 14px; }

        .baris2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
        .baris1 { margin-bottom: 14px; }
        .baris1:last-child, .baris2:last-child { margin-bottom: 0; }
        .kartu label.lbl { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; }
        .kartu input[type=text], .kartu input[type=date], .kartu select, .kartu textarea {
            width: 100%; min-height: 46px; padding: 0 16px; border: 1.5px solid transparent; border-radius: 16px;
            background: var(--field); box-shadow: inset 0 2px 5px rgba(42, 42, 92, 0.07);
            font: inherit; font-size: 13.5px; font-weight: 600; color: var(--ink-strong); outline: none;
        }
        .kartu textarea { padding: 12px 16px; min-height: 96px; resize: vertical; line-height: 1.5; }
        .kartu input::placeholder, .kartu textarea::placeholder { color: #a4a3b3; font-weight: 500; }
        .kartu input:focus, .kartu select:focus, .kartu textarea:focus { background: #fff; border-color: var(--ink); box-shadow: 0 0 0 4px rgba(42, 42, 92, 0.1); }
        .kartu .tidak-valid { border-color: #e08a97; background: #fff8f9; }
        .bantu { margin-top: 5px; font-size: 11.5px; font-weight: 500; color: var(--muted); }
        .galat-field { margin-top: 5px; font-size: 11.5px; font-weight: 700; color: #a3153f; }
        .galat-field:empty { display: none; }
        .hitung { margin-top: 5px; text-align: right; font-size: 11.5px; font-weight: 500; color: var(--muted); }
        .wajib { color: #a3153f; }

        .status-otomatis { min-height: 46px; padding: 0 16px; border-radius: 16px; background: #ebeaf3; display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 800; }
        .status-otomatis small { font-size: 11px; font-weight: 600; color: var(--muted); }

        .peringatan { margin-top: 14px; padding: 12px 16px; border-radius: 14px; background: #fdf1d3; color: #7a4f00; font-size: 12.5px; font-weight: 700; }
        .peringatan.aman { background: #e9f6ef; color: #1f6b44; }

        .dropzone { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; min-height: 150px; padding: 18px; border: 2px dashed rgba(42, 42, 92, 0.22); border-radius: 20px; background: var(--field); text-align: center; cursor: pointer; }
        .dropzone:hover, .dropzone.seret, .dropzone:focus-within { border-color: var(--ink); background: #fff; }
        .dropzone b { font-size: 13px; font-weight: 800; }
        .dropzone small { font-size: 11px; font-weight: 500; color: var(--muted); }
        .dropzone.tidak-valid { border-color: #e08a97; }
        .pratinjau { display: none; align-items: center; gap: 12px; margin-top: 12px; padding: 10px; border-radius: 16px; background: #fff; }
        .pratinjau.tampil { display: flex; }
        .pratinjau img { width: 56px; height: 56px; object-fit: cover; border-radius: 12px; background: var(--field); }
        .pratinjau div { min-width: 0; flex: 1; }
        .pratinjau b { display: block; font-size: 12.5px; font-weight: 800; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .pratinjau span { font-size: 11px; font-weight: 500; color: var(--muted); }
        .pratinjau button { border: 0; background: none; color: #a3153f; font: inherit; font-size: 12px; font-weight: 700; cursor: pointer; }
        .tips { margin-top: 12px; list-style: none; display: grid; gap: 4px; font-size: 11.5px; font-weight: 500; color: var(--muted); }

        .ringkasan { background: var(--ink); color: #fff; border-radius: 26px; padding: 22px; }
        .ringkasan small { display: block; font-size: 10.5px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: rgba(255, 255, 255, 0.7); margin-bottom: 12px; }
        .ringkasan .brs { display: flex; justify-content: space-between; gap: 12px; padding: 6px 0; font-size: 14px; font-weight: 500; }
        .ringkasan .brs b { font-weight: 800; text-align: right; }

        .aksi-form { margin-top: 22px; display: flex; justify-content: center; gap: 12px; }
        .aksi-form .btn-primary { min-width: 170px; justify-content: center; }
        .btn-primary[disabled] { opacity: 0.65; cursor: wait; }

        .modal { position: fixed; inset: 0; z-index: 60; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(42, 42, 92, 0.45); }
        .modal[hidden] { display: none; }
        .modal-card { width: 100%; max-width: 460px; max-height: 94vh; overflow-y: auto; background: #fdfdfc; border-radius: 28px; padding: 26px 24px 22px; box-shadow: 0 40px 80px -30px rgba(20, 20, 60, 0.6); }
        .modal-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
        .modal-head h2 { font-size: 18px; font-weight: 800; letter-spacing: -0.3px; }
        .modal-head p { margin-top: 4px; font-size: 12.5px; font-weight: 500; color: var(--muted); }
        .tutup { width: 34px; height: 34px; border: 0; border-radius: 50%; background: #fff; color: var(--ink); font-size: 18px; line-height: 1; cursor: pointer; box-shadow: 0 6px 14px -8px rgba(42, 42, 92, 0.4); flex-shrink: 0; }
        .modal .peringatan { margin: 16px 0 12px; }
        .mirip { display: block; padding: 14px 16px; border-radius: 18px; background: var(--field); text-decoration: none; margin-bottom: 8px; }
        .mirip b { display: block; font-size: 14px; font-weight: 800; }
        .mirip span { font-size: 12px; font-weight: 500; color: var(--muted); }
        .modal .aksi { display: flex; justify-content: flex-end; gap: 10px; margin-top: 14px; }

        @media (max-width: 980px) { .tata { grid-template-columns: 1fr; } }
        @media (max-width: 560px) { .baris2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
@php
    $dibayarAwal = (float) preg_replace('/\D/', '', (string) old('nominal_dibayar', 0));
    $transaksiAwal = (float) preg_replace('/\D/', '', (string) old('nominal_transaksi', 0));
    $hari = \App\Models\Kuitansi::HARI_PENGINGAT;
    $galatLain = collect($errors->keys())->reject(fn ($k) => $k === 'duplikat')->isNotEmpty();
@endphp

<div class="shell">
    @include('partials.topnav', ['aktifMenu' => 'kuitansi-baru'])

    <main class="board">
        <div class="page-head">
            <div>
                <h1>Tambah Kuitansi Baru</h1>
                <p>Lengkapi data transaksi agar tempo dan status pembayaran dapat dipantau.</p>
            </div>
        </div>

        @if ($errors->has('duplikat'))
            <div class="alert err" role="alert">{{ $errors->first('duplikat') }}</div>
        @endif
        @if ($galatLain)
            <div class="alert err" role="alert">Beberapa isian belum sesuai. Periksa pesan di bawah kolom yang bertanda merah.</div>
        @endif

        <div class="steps" aria-label="Langkah pengisian">
            <span class="step" id="step1">1 Informasi transaksi</span>
            <span class="step" id="step2">2 Bukti &amp; pembayaran</span>
            <span class="step" id="step3">3 Simpan arsip</span>
        </div>

        <form method="POST" action="{{ route('kuitansi.store') }}" enctype="multipart/form-data" id="formKuitansi" novalidate>
            @csrf
            <input type="hidden" name="konfirmasi_duplikat" id="konfirmasiDuplikat" value="{{ $errors->has('duplikat') ? '1' : '' }}">

            <div class="tata">
                <div class="kolom">
                    <section class="kartu" aria-labelledby="h-info">
                        <h2 id="h-info">Informasi kuitansi</h2>

                        <div class="baris2">
                            <div>
                                <label class="lbl" for="fTanggal">Tanggal kuitansi <span class="wajib">*</span></label>
                                <input type="date" id="fTanggal" name="tanggal_kuitansi" value="{{ old('tanggal_kuitansi', $hariIni) }}" @class(['tidak-valid' => $errors->has('tanggal_kuitansi')])>
                                <p class="galat-field" id="err-tanggal_kuitansi">{{ $errors->first('tanggal_kuitansi') }}</p>
                            </div>
                            <div>
                                <label class="lbl" for="fNama">Nama pelanggan / supplier <span class="wajib">*</span></label>
                                <input type="text" id="fNama" name="nama_pihak" maxlength="255" value="{{ old('nama_pihak') }}" placeholder="Contoh: Warung Bu Sari" autocomplete="off" @class(['tidak-valid' => $errors->has('nama_pihak')])>
                                <p class="galat-field" id="err-nama_pihak">{{ $errors->first('nama_pihak') }}</p>
                            </div>
                        </div>

                        <div class="baris2">
                            <div>
                                <label class="lbl" for="fJenis">Jenis kuitansi <span class="wajib">*</span></label>
                                <select id="fJenis" name="jenis">
                                    <option value="piutang" @selected(old('jenis', 'piutang') === 'piutang')>Piutang — pelanggan berutang</option>
                                    <option value="utang" @selected(old('jenis') === 'utang')>Utang — toko berutang ke supplier</option>
                                </select>
                                <p class="galat-field" id="err-jenis">{{ $errors->first('jenis') }}</p>
                            </div>
                            <div>
                                <label class="lbl" for="fNominal">Nominal transaksi (Rp) <span class="wajib">*</span></label>
                                <input type="text" id="fNominal" name="nominal_transaksi" inputmode="numeric" autocomplete="off" value="{{ $transaksiAwal > 0 ? number_format($transaksiAwal, 0, ',', '.') : '' }}" placeholder="0" @class(['tidak-valid' => $errors->has('nominal_transaksi')])>
                                <p class="galat-field" id="err-nominal_transaksi">{{ $errors->first('nominal_transaksi') }}</p>
                            </div>
                        </div>

                        <div class="baris1">
                            <label class="lbl" for="fFisik">Nomor kuitansi fisik <span style="font-weight:500;color:var(--muted)">(jika ada)</span></label>
                            <input type="text" id="fFisik" name="nomor_kuitansi_fisik" maxlength="50" value="{{ old('nomor_kuitansi_fisik') }}" placeholder="Nomor pada dokumen asli, boleh dikosongkan" autocomplete="off" @class(['tidak-valid' => $errors->has('nomor_kuitansi_fisik')])>
                            <p class="bantu">Nomor arsip dibuat otomatis oleh sistem dan tidak dapat diubah. Nomor ini hanya mencatat nomor yang tertulis di kuitansi asli.</p>
                            <p class="galat-field" id="err-nomor_kuitansi_fisik">{{ $errors->first('nomor_kuitansi_fisik') }}</p>
                        </div>

                        <div class="baris1">
                            <label class="lbl" for="fCatatan">Catatan transaksi</label>
                            <textarea id="fCatatan" name="catatan" maxlength="500" placeholder="Tuliskan barang, layanan, atau keterangan transaksi">{{ old('catatan') }}</textarea>
                            <p class="hitung"><span id="hitungCatatan">0</span>/500 karakter</p>
                            <p class="galat-field" id="err-catatan">{{ $errors->first('catatan') }}</p>
                        </div>
                    </section>

                    <section class="kartu" aria-labelledby="h-bayar">
                        <h2 id="h-bayar">Pembayaran &amp; jatuh tempo</h2>

                        <div class="baris2">
                            <div>
                                <label class="lbl" for="fDibayar">Nominal dibayar (Rp)</label>
                                <input type="text" id="fDibayar" name="nominal_dibayar" inputmode="numeric" autocomplete="off" value="{{ $dibayarAwal > 0 ? number_format($dibayarAwal, 0, ',', '.') : '' }}" placeholder="0" @class(['tidak-valid' => $errors->has('nominal_dibayar')])>
                                <p class="bantu">Isi jika sudah ada pembayaran. Kosongkan jika belum ada.</p>
                                <p class="galat-field" id="err-nominal_dibayar">{{ $errors->first('nominal_dibayar') }}</p>
                            </div>
                            <div>
                                <span class="lbl" style="display:block;margin-bottom:6px;font-size:12px;font-weight:700;">Status awal</span>
                                <div class="status-otomatis"><span id="statusAwal">Belum lunas</span><small>dihitung otomatis</small></div>
                            </div>
                        </div>

                        <div class="baris2" id="grupBayar" @if ($dibayarAwal <= 0) hidden @endif>
                            <div>
                                <label class="lbl" for="fTglBayar">Tanggal pembayaran <span class="wajib">*</span></label>
                                <input type="date" id="fTglBayar" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $hariIni) }}" max="{{ $hariIni }}" @class(['tidak-valid' => $errors->has('tanggal_pembayaran')])>
                                <p class="bantu">Isi tanggal pembayaran yang sebenarnya.</p>
                                <p class="galat-field" id="err-tanggal_pembayaran">{{ $errors->first('tanggal_pembayaran') }}</p>
                            </div>
                            <div>
                                <label class="lbl" for="fMetode">Metode pembayaran <span class="wajib">*</span></label>
                                <select id="fMetode" name="metode_pembayaran" @class(['tidak-valid' => $errors->has('metode_pembayaran')])>
                                    <option value="Tunai" @selected(old('metode_pembayaran') === 'Tunai')>Tunai</option>
                                    <option value="Transfer bank" @selected(old('metode_pembayaran', 'Transfer bank') === 'Transfer bank')>Transfer</option>
                                </select>
                                <p class="galat-field" id="err-metode_pembayaran">{{ $errors->first('metode_pembayaran') }}</p>
                            </div>
                        </div>

                        <div class="baris1" id="grupTempo">
                            <label class="lbl" for="fTempo">Tanggal jatuh tempo <span class="wajib" id="tandaTempo">*</span></label>
                            <input type="date" id="fTempo" name="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo') }}" @class(['tidak-valid' => $errors->has('tanggal_jatuh_tempo')])>
                            <p class="bantu">Wajib diisi selama transaksi masih memiliki sisa.</p>
                            <p class="galat-field" id="err-tanggal_jatuh_tempo">{{ $errors->first('tanggal_jatuh_tempo') }}</p>
                        </div>

                        <div class="peringatan" id="teksPengingat" role="status">
                            BukuTempo menampilkan pengingat mulai H-{{ $hari }} sebelum jatuh tempo sampai transaksi dilunasi.
                        </div>
                    </section>
                </div>

                <div class="kolom">
                    <section class="kartu" aria-labelledby="h-foto">
                        <h2 id="h-foto">Unggah foto kuitansi <span class="wajib">*</span></h2>

                        <label class="dropzone @if ($errors->has('bukti')) tidak-valid @endif" id="dropzone" for="fBukti">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5"></path><polyline points="5 12 12 5 19 12"></polyline></svg>
                            <b>Tarik foto ke sini atau klik untuk pilih file</b>
                            <small>JPG atau PNG hingga 10 MB · satu foto wajib</small>
                            <input type="file" id="fBukti" name="bukti" accept="image/jpeg,image/png" class="sr-only">
                        </label>
                        <div class="pratinjau" id="pratinjau">
                            <img id="gambarPratinjau" alt="Pratinjau foto kuitansi">
                            <div><b id="namaFoto"></b><span id="ukuranFoto"></span></div>
                            <button type="button" id="hapusFoto">Ganti</button>
                        </div>
                        <p class="galat-field" id="err-bukti">{{ $errors->first('bukti') }}</p>

                        <ul class="tips">
                            <li>✓ Pastikan seluruh kuitansi terlihat</li>
                            <li>✓ Hindari bayangan dan pantulan</li>
                            <li>✓ Gunakan foto dengan fokus tajam</li>
                        </ul>
                    </section>

                    <section class="ringkasan" aria-labelledby="h-ringkas">
                        <small id="h-ringkas">Ringkasan arsip</small>
                        <div class="brs"><span>Nomor arsip sementara</span><b id="rNomor">{{ $nomorSementara }}</b></div>
                        <div class="brs"><span>Nominal</span><b id="rNominal">Rp0</b></div>
                        <div class="brs"><span>Sisa setelah disimpan</span><b id="rSisa">Rp0</b></div>
                    </section>
                </div>
            </div>

            <div class="aksi-form">
                <a href="{{ route('arsip-kuitansi.index') }}" class="btn-soft" style="height:44px;padding:0 24px;">Batal</a>
                <button type="submit" class="btn-primary" id="tombolSimpan">Simpan kuitansi</button>
            </div>
        </form>
    </main>
</div>

{{-- Peringatan arsip yang mirip: owner memeriksa apakah benar duplikat --}}
<div class="modal" id="modalDuplikat" hidden role="dialog" aria-modal="true" aria-labelledby="judulDuplikat">
    <div class="modal-card">
        <div class="modal-head">
            <div>
                <h2 id="judulDuplikat">Kemungkinan arsip duplikat</h2>
                <p>Periksa sebelum menyimpan kuitansi baru.</p>
            </div>
            <button type="button" class="tutup" data-tutup aria-label="Tutup">&times;</button>
        </div>
        <div class="peringatan" role="alert">Sudah ada kuitansi dengan nama, tanggal, dan nominal yang sama (atau nomor kuitansi fisik yang sama). Periksa dulu agar arsip tidak ganda.</div>
        <div id="daftarMirip"></div>
        <div class="aksi">
            <button type="button" class="btn-soft" data-tutup style="height:44px;padding:0 20px;">Periksa kembali</button>
            <button type="button" class="btn-primary" id="tetapSimpan">Tetap simpan</button>
        </div>
    </div>
</div>

<script>
    (function () {
        var HARI_PENGINGAT = {{ $hari }};
        var URL_NOMOR = @json(route('kuitansi.nomor-sementara'));
        var URL_CEK = @json(route('kuitansi.cek-duplikat'));
        var MAKS_BYTE = 10 * 1024 * 1024;
        var BOLEH = ['image/jpeg', 'image/png'];

        var $ = function (id) { return document.getElementById(id); };
        var form = $('formKuitansi'), tombol = $('tombolSimpan');
        var fTanggal = $('fTanggal'), fNama = $('fNama'), fJenis = $('fJenis'), fNominal = $('fNominal'), fFisik = $('fFisik');
        var fDibayar = $('fDibayar'), fTglBayar = $('fTglBayar'), fMetode = $('fMetode'), fTempo = $('fTempo'), fBukti = $('fBukti');

        function angka(v) { return parseInt(String(v || '').replace(/\D/g, ''), 10) || 0; }
        function rp(n) { return 'Rp' + n.toLocaleString('id-ID'); }
        function rapikan(input) { var n = angka(input.value); input.value = n ? n.toLocaleString('id-ID') : ''; }

        function galat(nama, pesan, input) {
            var el = $('err-' + nama);
            if (el) { el.textContent = pesan || ''; }
            if (input) { input.classList.toggle('tidak-valid', !!pesan); }
        }

        function tanggalMinus(iso, hari) {
            var d = new Date(iso + 'T00:00:00');
            if (isNaN(d.getTime())) { return ''; }
            d.setDate(d.getDate() - hari);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        // Hitung ulang sisa, status otomatis, kolom yang wajib, dan ringkasan.
        function hitung() {
            var transaksi = angka(fNominal.value), dibayar = angka(fDibayar.value);
            var sisa = transaksi - dibayar;
            var adaBayar = dibayar > 0;
            var lunas = transaksi > 0 && sisa <= 0;

            $('grupBayar').hidden = !adaBayar;
            $('grupTempo').hidden = lunas;
            $('tandaTempo').style.display = lunas ? 'none' : '';

            $('statusAwal').textContent = !adaBayar ? 'Belum lunas' : (lunas ? 'Lunas' : 'DP');
            $('rNominal').textContent = rp(transaksi);
            $('rSisa').textContent = rp(Math.max(0, sisa));

            var teks = $('teksPengingat');
            if (lunas) {
                teks.textContent = 'Transaksi langsung lunas tetap diarsipkan dan tidak masuk pengingat jatuh tempo.';
                teks.classList.add('aman');
            } else {
                var tgl = fTempo.value ? tanggalMinus(fTempo.value, HARI_PENGINGAT) : '';
                teks.textContent = 'BukuTempo menampilkan pengingat mulai H-' + HARI_PENGINGAT + ' sebelum jatuh tempo' + (tgl ? ' (' + tgl + ')' : '') + ' sampai transaksi dilunasi.';
                teks.classList.remove('aman');
            }

            galat('nominal_dibayar',
                (dibayar > transaksi && transaksi > 0) ? 'Nominal dibayar tidak boleh melebihi nominal transaksi.' : '',
                fDibayar);

            perbaruiLangkah();
        }

        function perbaruiLangkah() {
            var transaksi = angka(fNominal.value), dibayar = angka(fDibayar.value), sisa = transaksi - dibayar;
            var info = fTanggal.value && fNama.value.trim() && transaksi > 0;
            var bukti = fBukti.files.length === 1
                && (dibayar === 0 || (fTglBayar.value && fMetode.value))
                && (sisa <= 0 || fTempo.value);
            $('step1').classList.toggle('selesai', !!info);
            $('step2').classList.toggle('selesai', !!bukti);
        }

        // ----- Nomor arsip sementara mengikuti tanggal kuitansi -----
        function perbaruiNomor() {
            if (!fTanggal.value) { return; }
            fetch(URL_NOMOR + '?tanggal=' + encodeURIComponent(fTanggal.value), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (d) { if (d && d.nomor) { $('rNomor').textContent = d.nomor; } })
                .catch(function () {});
        }

        // ----- Foto: tepat satu, JPG atau PNG, maksimal 10 MB -----
        var urlPratinjau = null;
        function kosongkanFoto() {
            fBukti.value = '';
            $('pratinjau').classList.remove('tampil');
            if (urlPratinjau) { URL.revokeObjectURL(urlPratinjau); urlPratinjau = null; }
            perbaruiLangkah();
        }

        function periksaFoto(file) {
            if (BOLEH.indexOf(file.type) === -1) { return 'Foto harus berformat JPG atau PNG. PDF tidak diterima.'; }
            if (file.size > MAKS_BYTE) { return 'Ukuran foto maksimal 10 MB.'; }
            return '';
        }

        function tampilkanFoto() {
            var file = fBukti.files[0];
            if (!file) { kosongkanFoto(); return; }
            var pesan = periksaFoto(file);
            if (pesan) { kosongkanFoto(); galat('bukti', pesan, $('dropzone')); return; }
            galat('bukti', '', $('dropzone'));
            if (urlPratinjau) { URL.revokeObjectURL(urlPratinjau); }
            urlPratinjau = URL.createObjectURL(file);
            $('gambarPratinjau').src = urlPratinjau;
            $('namaFoto').textContent = file.name;
            $('ukuranFoto').textContent = file.size < 1024 * 1024
                ? Math.max(1, Math.round(file.size / 1024)) + ' KB'
                : (file.size / 1024 / 1024).toFixed(2).replace('.', ',') + ' MB';
            $('pratinjau').classList.add('tampil');
            perbaruiLangkah();
        }

        fBukti.addEventListener('change', function () {
            if (fBukti.files.length > 1) { kosongkanFoto(); galat('bukti', 'Satu arsip hanya boleh memiliki satu foto.', $('dropzone')); return; }
            tampilkanFoto();
        });
        $('hapusFoto').addEventListener('click', function () { kosongkanFoto(); fBukti.click(); });

        var zona = $('dropzone');
        ['dragenter', 'dragover'].forEach(function (n) { zona.addEventListener(n, function (e) { e.preventDefault(); zona.classList.add('seret'); }); });
        ['dragleave', 'drop'].forEach(function (n) { zona.addEventListener(n, function (e) { e.preventDefault(); zona.classList.remove('seret'); }); });
        zona.addEventListener('drop', function (e) {
            var files = e.dataTransfer ? e.dataTransfer.files : [];
            if (files.length > 1) { kosongkanFoto(); galat('bukti', 'Satu arsip hanya boleh memiliki satu foto.', zona); return; }
            if (files.length === 1) {
                var dt = new DataTransfer();
                dt.items.add(files[0]);
                fBukti.files = dt.files;
                tampilkanFoto();
            }
        });

        // ----- Peringatan arsip mirip -----
        var modal = $('modalDuplikat');
        function tutupModal() { modal.hidden = true; tombol.disabled = false; tombol.textContent = 'Simpan kuitansi'; }
        function kirim() { form.dataset.siap = '1'; tombol.disabled = true; tombol.textContent = 'Menyimpan…'; form.submit(); }

        function tampilkanMirip(daftar) {
            var kotak = $('daftarMirip');
            kotak.innerHTML = '';
            daftar.forEach(function (k) {
                var a = document.createElement('a');
                a.className = 'mirip';
                a.href = k.url; a.target = '_blank'; a.rel = 'noopener';
                var b = document.createElement('b'); b.textContent = k.nama;
                var s = document.createElement('span'); s.textContent = k.nomor + ' · ' + k.tanggal + ' · ' + k.nominal;
                a.appendChild(b); a.appendChild(s); kotak.appendChild(a);
            });
            tombol.disabled = false;
            tombol.textContent = 'Simpan kuitansi';
            modal.hidden = false;
            $('tetapSimpan').focus();
        }

        document.querySelectorAll('[data-tutup]').forEach(function (b) { b.addEventListener('click', tutupModal); });
        modal.addEventListener('click', function (e) { if (e.target === modal) { tutupModal(); } });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) { tutupModal(); } });
        $('tetapSimpan').addEventListener('click', function () { $('konfirmasiDuplikat').value = '1'; modal.hidden = true; kirim(); });

        // ----- Pemeriksaan kelengkapan sebelum kirim -----
        function validasi() {
            var transaksi = angka(fNominal.value), dibayar = angka(fDibayar.value), sisa = transaksi - dibayar;
            var pertama = null;
            function cek(ok, nama, pesan, input) {
                galat(nama, ok ? '' : pesan, input);
                if (!ok && !pertama) { pertama = input; }
            }
            cek(!!fTanggal.value, 'tanggal_kuitansi', 'Tanggal kuitansi wajib diisi.', fTanggal);
            cek(!!fNama.value.trim(), 'nama_pihak', 'Nama pelanggan atau supplier wajib diisi.', fNama);
            cek(transaksi > 0, 'nominal_transaksi', 'Nominal transaksi wajib diisi (minimal Rp1).', fNominal);
            cek(!(dibayar > transaksi && transaksi > 0), 'nominal_dibayar', 'Nominal dibayar tidak boleh melebihi nominal transaksi.', fDibayar);
            if (dibayar > 0) {
                cek(!!fTglBayar.value, 'tanggal_pembayaran', 'Tanggal pembayaran wajib diisi karena sudah ada pembayaran.', fTglBayar);
                cek(!fTglBayar.value || fTglBayar.value <= fTglBayar.max, 'tanggal_pembayaran', 'Tanggal pembayaran tidak boleh di masa depan.', fTglBayar);
            }
            if (sisa > 0) {
                cek(!!fTempo.value, 'tanggal_jatuh_tempo', 'Tanggal jatuh tempo wajib diisi karena transaksi masih memiliki sisa.', fTempo);
            }
            var file = fBukti.files[0];
            var pesanFoto = !file ? 'Foto kuitansi wajib diunggah (tepat satu foto).' : periksaFoto(file);
            galat('bukti', pesanFoto, $('dropzone'));
            if (pesanFoto && !pertama) { pertama = fBukti; }
            if (pertama) { pertama.scrollIntoView({ behavior: 'smooth', block: 'center' }); pertama.focus(); }
            return !pertama;
        }

        form.addEventListener('submit', function (e) {
            if (form.dataset.siap === '1') { return; }
            e.preventDefault();
            if (!validasi()) { return; }
            if ($('konfirmasiDuplikat').value === '1') { kirim(); return; }

            tombol.disabled = true;
            tombol.textContent = 'Memeriksa…';
            fetch(URL_CEK, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    nama_pihak: fNama.value,
                    tanggal_kuitansi: fTanggal.value,
                    nominal_transaksi: fNominal.value,
                    nomor_kuitansi_fisik: fFisik.value
                })
            })
                .then(function (r) { return r.ok ? r.json() : { duplikat: [] }; })
                .catch(function () { return { duplikat: [] }; })
                .then(function (d) {
                    if (d.duplikat && d.duplikat.length) { tampilkanMirip(d.duplikat); } else { kirim(); }
                });
        });

        // ----- Pasang -----
        [fNominal, fDibayar].forEach(function (i) {
            i.addEventListener('input', function () { rapikan(i); hitung(); });
        });
        [fTempo, fTglBayar, fMetode, fNama, fJenis].forEach(function (i) { i.addEventListener('input', hitung); });
        fTanggal.addEventListener('change', function () { perbaruiNomor(); perbaruiLangkah(); });
        // Mengubah isian yang dipakai pemeriksaan duplikat menghapus konfirmasi sebelumnya.
        [fNama, fTanggal, fNominal, fFisik].forEach(function (i) {
            i.addEventListener('input', function () { $('konfirmasiDuplikat').value = ''; });
        });
        fFisik.addEventListener('input', perbaruiLangkah);
        // Pesan galat sebuah kolom hilang begitu kolom itu diubah.
        [[fTanggal, 'tanggal_kuitansi'], [fNama, 'nama_pihak'], [fNominal, 'nominal_transaksi'], [fFisik, 'nomor_kuitansi_fisik'],
         [fTglBayar, 'tanggal_pembayaran'], [fMetode, 'metode_pembayaran'], [fTempo, 'tanggal_jatuh_tempo']].forEach(function (p) {
            ['input', 'change'].forEach(function (ev) { p[0].addEventListener(ev, function () { galat(p[1], '', p[0]); }); });
        });
        $('fCatatan').addEventListener('input', function () { $('hitungCatatan').textContent = this.value.length; });

        $('hitungCatatan').textContent = $('fCatatan').value.length;
        hitung();
        perbaruiNomor();
    })();
</script>
</body>
</html>
