<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kuitansi {{ $kuitansi->nomor_kuitansi }} | BukuTempo</title>
    @include('partials.topnav-styles')
    @include('partials.page-styles')
    @include('arsip-kuitansi._detail-styles')
</head>
<body>
@php
    $rp = fn ($angka) => \App\Models\Kuitansi::rupiah($angka);
    $bukti = $kuitansi->bukti_info;
    $dibatalkan = $kuitansi->dibatalkan_pada !== null;
    $lunas = ! $dibatalkan && ($kuitansi->status_pembayaran === 'lunas' || $sisa <= 0);
    $bisaTambah = ! $dibatalkan && ! $lunas;
    $labelPihak = $kuitansi->jenis === 'piutang' ? 'Pelanggan' : 'Supplier';
    $labelSisa = $kuitansi->jenis === 'piutang' ? 'Sisa piutang' : 'Sisa utang';
    $semuaPembayaran = $kuitansi->semuaPembayaran;

    // Modal yang harus dibuka lagi bila validasi gagal, dan nilai/galat khusus modal itu.
    $modalAktif = old('_modal');
    $nilai = fn ($modal, $kolom, $bawaan = null) => $modalAktif === $modal ? old($kolom, $bawaan) : $bawaan;
    $galat = fn ($modal, $kolom) => $modalAktif === $modal ? $errors->first($kolom) : '';

    // Penanda jatuh tempo hanya untuk tagihan yang masih bersisa.
    $tempoTeks = null;
    $tempoKelas = 'netral';
    if (! $dibatalkan && $sisa > 0 && $kuitansi->tanggal_jatuh_tempo) {
        [$tempoTeks, $tempoKelas] = \App\Models\Kuitansi::badgeTempo($kuitansi->tanggal_jatuh_tempo, now()->startOfDay());
    }
@endphp

<div class="shell">
    @include('partials.topnav', ['aktifMenu' => 'arsip'])

    <main class="board">
        <a href="{{ route('arsip-kuitansi.index') }}" class="kembali">← Kembali ke Arsip</a>

        <div class="page-head detail-head">
            <div>
                <h1>Detail Kuitansi {{ $kuitansi->nomor_kuitansi }}</h1>
                <p>
                    Dibuat {{ $kuitansi->tanggal_kuitansi->translatedFormat('d M Y') }}
                    ·
                    @if ($dibatalkan)
                        <span class="badge dibatalkan">Dibatalkan</span>
                    @else
                        <span class="badge {{ $kuitansi->status_pembayaran }}">{{ \App\Models\Kuitansi::labelStatus($kuitansi->status_pembayaran) }}</span>
                    @endif
                </p>
            </div>
            @unless ($dibatalkan)
                <div class="aksi-head">
                    <button type="button" class="btn-soft" data-buka="modal-edit-kuitansi">Edit kuitansi</button>
                    @if ($bisaTambah)
                        <button type="button" class="btn-primary kecil" data-buka="modal-tambah">+ Tambah pembayaran</button>
                    @endif
                    <button type="button" class="btn-soft bahaya" data-buka="modal-batal-transaksi">Batalkan transaksi</button>
                </div>
            @endunless
        </div>

        @if (session('status'))
            <div class="alert ok" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->has('transaksi'))
            <div class="alert err" role="alert">{{ $errors->first('transaksi') }}</div>
        @endif

        @if ($dibatalkan)
            <div class="banner-batal" role="status">
                <b>Transaksi ini dibatalkan pada {{ $kuitansi->dibatalkan_pada->translatedFormat('d M Y, H.i') }}.</b>
                <span>Alasan: {{ $kuitansi->alasan_pembatalan }}</span>
                @if ($kuitansi->uang_dikembalikan !== null)
                    <span>Penyelesaian uang: {{ $kuitansi->uang_dikembalikan ? 'dikembalikan penuh' : 'tidak dikembalikan' }}.</span>
                @endif
            </div>
        @endif

        <div class="ringkas">
            <span>Terbayar <b>{{ $rp($terbayar) }}</b> dari <b>{{ $rp($kuitansi->nominal_transaksi) }}</b> · Sisa <b>{{ $rp($sisa) }}</b></span>
            <span class="tempo-ringkas {{ $dibatalkan ? 'netral' : ($lunas ? 'lunas' : $tempoKelas) }}">
                @if ($dibatalkan)
                    Transaksi dibatalkan
                @elseif ($lunas)
                    Lunas
                @elseif ($kuitansi->tanggal_jatuh_tempo)
                    Jatuh tempo {{ $kuitansi->tanggal_jatuh_tempo->translatedFormat('d M Y') }} · {{ $tempoTeks }}
                @else
                    Tanpa jatuh tempo
                @endif
            </span>
        </div>

        <div class="kolom">
            <div class="kolom-kiri">
                <section class="kartu" aria-labelledby="h-rincian">
                    <h2 id="h-rincian">Rincian transaksi</h2>
                    <div class="kv"><span>Nomor arsip</span><b>{{ $kuitansi->nomor_kuitansi }}</b></div>
                    <div class="kv"><span>Nomor kuitansi fisik</span><b>{{ $kuitansi->nomor_kuitansi_fisik ?: '-' }}</b></div>
                    <div class="kv"><span>Tanggal kuitansi</span><b>{{ $kuitansi->tanggal_kuitansi->translatedFormat('d M Y') }}</b></div>
                    <div class="kv"><span>{{ $labelPihak }}</span><b>{{ $kuitansi->nama_pihak }}</b></div>
                    <div class="kv"><span>Jenis</span><b>{{ ucfirst($kuitansi->jenis) }}</b></div>
                    <div class="kv"><span>Nominal transaksi</span><b>{{ $rp($kuitansi->nominal_transaksi) }}</b></div>
                    <div class="kv"><span>Status saat ini</span><b>{{ $dibatalkan ? 'Dibatalkan' : \App\Models\Kuitansi::labelStatus($kuitansi->status_pembayaran) }}</b></div>
                    <div class="kv"><span>Jatuh tempo</span><b>{{ $kuitansi->tanggal_jatuh_tempo?->translatedFormat('d M Y') ?? '-' }}</b></div>
                    <div class="kv"><span>Catatan</span><b>{{ $kuitansi->keterangan ?: '-' }}</b></div>
                </section>

                <section class="kartu" aria-labelledby="h-riwayat">
                    <h2 id="h-riwayat">Riwayat pembayaran</h2>
                    @forelse ($semuaPembayaran as $bayar)
                        @php $batal = $bayar->status !== 'aktif'; @endphp
                        <div class="bayar {{ $batal ? 'is-batal' : '' }}">
                            <div class="bayar-info">
                                <b>{{ $bayar->metode_pembayaran ? \App\Models\Pembayaran::labelMetode($bayar->metode_pembayaran) : 'Pembayaran' }}
                                    <span class="badge {{ $batal ? 'dibatalkan' : 'aktif' }}">{{ $batal ? 'Dibatalkan' : 'Aktif' }}</span>
                                </b>
                                <small>
                                    {{ $bayar->tanggal_pembayaran->translatedFormat('d M Y') }}
                                    @if ($bayar->catatan) · {{ $bayar->catatan }} @elseif ($bayar->dicatat_oleh) · Dicatat {{ $bayar->dicatat_oleh }} @endif
                                </small>
                                @if ($batal && $bayar->alasan_pembatalan)
                                    <small class="alasan">Alasan batal: {{ $bayar->alasan_pembatalan }}</small>
                                @endif
                            </div>
                            <span class="bayar-nominal">{{ $rp($bayar->nominal) }}</span>
                            @if (! $batal && ! $dibatalkan)
                                <button type="button" class="mini" data-buka="modal-edit-bayar-{{ $bayar->id }}">Edit</button>
                                <button type="button" class="mini bahaya" data-buka="modal-batal-bayar-{{ $bayar->id }}">Batalkan</button>
                            @endif
                        </div>
                    @empty
                        <p class="kosong-kecil">Belum ada pembayaran.</p>
                    @endforelse
                    <div class="sisa"><span>{{ $labelSisa }}</span><b>{{ $rp($sisa) }}</b></div>
                </section>

                <section class="kartu" aria-labelledby="h-koreksi">
                    <h2 id="h-koreksi">Riwayat koreksi dan pembatalan</h2>
                    @forelse ($kuitansi->koreksi as $log)
                        <div class="log">
                            <b>{{ $log->ringkasan }}</b>
                            @if ($log->detail)<span>{{ $log->detail }}</span>@endif
                            @if ($log->alasan)<span class="alasan">Alasan: {{ $log->alasan }}</span>@endif
                            <small>{{ $log->created_at->translatedFormat('d M Y, H.i') }}@if ($log->dicatat_oleh) · oleh {{ $log->dicatat_oleh }}@endif</small>
                        </div>
                    @empty
                        <p class="kosong-kecil">Belum ada koreksi atau pembatalan.</p>
                    @endforelse
                </section>
            </div>

            <div class="kolom-kanan">
                <section class="kartu" aria-labelledby="h-foto">
                    <h2 id="h-foto">Foto kuitansi
                        @if ($bukti['has_bukti'])
                            <a href="{{ $bukti['bukti_kuitansi_url'] }}" target="_blank" rel="noopener" class="link-kecil">Buka penuh</a>
                        @endif
                    </h2>
                    @if ($bukti['has_bukti'])
                        <img class="foto" src="{{ $bukti['bukti_kuitansi_url'] }}" alt="Foto kuitansi {{ $kuitansi->nomor_kuitansi }}" onerror="this.onerror=null;this.src='{{ $bukti['fallback_bukti_url'] }}'">
                        <p class="kecil-muted">{{ $bukti['bukti_kuitansi_name'] }} • {{ $bukti['bukti_kuitansi_meta'] }}</p>
                        <a href="{{ $bukti['bukti_kuitansi_url'] }}" download class="btn-soft unduh">⬇ Unduh bukti</a>
                    @else
                        <div class="foto foto-kosong" aria-hidden="true">
                            <svg width="96" height="72" viewBox="0 0 96 72" fill="none"><rect x="14" y="6" width="68" height="58" rx="5" fill="#fff" stroke="#d9d8d3" transform="rotate(-4 48 36)"/><text x="48" y="22" text-anchor="middle" font-size="7" font-weight="700" fill="#6b6b86" transform="rotate(-4 48 36)">NOTA</text><rect x="24" y="30" width="48" height="3" rx="1.5" fill="#d9d8d3" transform="rotate(-4 48 36)"/><rect x="24" y="38" width="40" height="3" rx="1.5" fill="#d9d8d3" transform="rotate(-4 48 36)"/><rect x="24" y="48" width="30" height="5" rx="2" fill="#2a2a5c" transform="rotate(-4 48 36)"/></svg>
                        </div>
                        <p class="kecil-muted">Belum ada foto diunggah</p>
                    @endif
                </section>
            </div>
        </div>
    </main>
</div>

{{-- ============ MODAL ============ --}}

@if ($bisaTambah)
<dialog class="modal" id="modal-tambah" aria-labelledby="jdl-tambah">
    <form method="POST" action="{{ route('pembayaran.store', $kuitansi) }}" class="modal-box" data-sisa="{{ (float) $sisa }}">
        @csrf
        <input type="hidden" name="redirect_to" value="arsip">
        <input type="hidden" name="_modal" value="modal-tambah">
        <div class="modal-head">
            <div>
                <h3 id="jdl-tambah">Tambah pembayaran</h3>
                <p>Catat pembayaran baru untuk {{ $kuitansi->nomor_kuitansi }} · {{ $kuitansi->nama_pihak }}</p>
            </div>
            <button type="button" class="modal-x" data-tutup aria-label="Tutup">×</button>
        </div>
        <div class="grid2">
            <div class="fld">
                <label for="tb-tanggal">Tanggal pembayaran</label>
                <input id="tb-tanggal" name="tanggal_pembayaran" type="date" class="inp" max="{{ now()->toDateString() }}" value="{{ $nilai('modal-tambah', 'tanggal_pembayaran', now()->toDateString()) }}" required>
                @if ($m = $galat('modal-tambah', 'tanggal_pembayaran'))<div class="err">{{ $m }}</div>@endif
            </div>
            <div class="fld">
                <label for="tb-nominal">Jumlah (Rp)</label>
                <input id="tb-nominal" name="nominal" type="number" min="1" max="{{ (int) $sisa }}" step="1" class="inp" value="{{ $nilai('modal-tambah', 'nominal') }}" placeholder="0" required>
                @if ($m = $galat('modal-tambah', 'nominal'))<div class="err">{{ $m }}</div>@endif
            </div>
        </div>
        <div class="fld">
            <label for="tb-metode">Metode</label>
            <select id="tb-metode" name="metode_pembayaran" class="inp">
                @foreach (['Tunai', 'Transfer bank'] as $metode)
                    <option value="{{ $metode }}" @selected($nilai('modal-tambah', 'metode_pembayaran') === $metode)>{{ \App\Models\Pembayaran::labelMetode($metode) }}</option>
                @endforeach
            </select>
            @if ($m = $galat('modal-tambah', 'metode_pembayaran'))<div class="err">{{ $m }}</div>@endif
        </div>
        <div class="fld">
            <label for="tb-catatan">Catatan</label>
            <input id="tb-catatan" name="catatan" type="text" class="inp" maxlength="255" value="{{ $nilai('modal-tambah', 'catatan') }}">
        </div>
        <div class="info" data-ringkasan>Sisa saat ini {{ $rp($sisa) }}. Setelah disimpan: {{ $rp($sisa) }}.</div>
        <div class="modal-foot">
            <button type="button" class="btn-soft" data-tutup>Batal</button>
            <button type="submit" class="btn-primary kecil">Simpan pembayaran</button>
        </div>
    </form>
</dialog>
@endif

@unless ($dibatalkan)
<dialog class="modal" id="modal-edit-kuitansi" aria-labelledby="jdl-edit-kuitansi">
    <form method="POST" action="{{ route('arsip-kuitansi.update', $kuitansi) }}" enctype="multipart/form-data" class="modal-box">
        @csrf
        @method('PUT')
        <input type="hidden" name="_modal" value="modal-edit-kuitansi">
        <div class="modal-head">
            <div>
                <h3 id="jdl-edit-kuitansi">Edit kuitansi</h3>
                <p>Ubah data kuitansi {{ $kuitansi->nomor_kuitansi }}, termasuk penggantian foto.</p>
            </div>
            <button type="button" class="modal-x" data-tutup aria-label="Tutup">×</button>
        </div>
        <div class="grid2">
            <div class="fld">
                <label for="ek-nama">Nama pelanggan / supplier</label>
                <input id="ek-nama" name="nama_pihak" type="text" class="inp" maxlength="255" value="{{ $nilai('modal-edit-kuitansi', 'nama_pihak', $kuitansi->nama_pihak) }}" required>
                @if ($m = $galat('modal-edit-kuitansi', 'nama_pihak'))<div class="err">{{ $m }}</div>@endif
            </div>
            <div class="fld">
                <label for="ek-tanggal">Tanggal kuitansi</label>
                <input id="ek-tanggal" name="tanggal_kuitansi" type="date" class="inp" value="{{ $nilai('modal-edit-kuitansi', 'tanggal_kuitansi', $kuitansi->tanggal_kuitansi->toDateString()) }}" required>
                @if ($m = $galat('modal-edit-kuitansi', 'tanggal_kuitansi'))<div class="err">{{ $m }}</div>@endif
            </div>
            <div class="fld">
                <label for="ek-nominal">Nominal transaksi (Rp)</label>
                <input id="ek-nominal" name="nominal_transaksi" type="number" min="1" step="1" class="inp" value="{{ $nilai('modal-edit-kuitansi', 'nominal_transaksi', (int) $kuitansi->nominal_transaksi) }}" required>
                @if ($m = $galat('modal-edit-kuitansi', 'nominal_transaksi'))<div class="err">{{ $m }}</div>@endif
            </div>
            <div class="fld">
                <label for="ek-tempo">Jatuh tempo</label>
                <input id="ek-tempo" name="tanggal_jatuh_tempo" type="date" class="inp" value="{{ $nilai('modal-edit-kuitansi', 'tanggal_jatuh_tempo', $kuitansi->tanggal_jatuh_tempo?->toDateString()) }}">
                @if ($m = $galat('modal-edit-kuitansi', 'tanggal_jatuh_tempo'))<div class="err">{{ $m }}</div>@endif
            </div>
        </div>
        <div class="fld">
            <label for="ek-fisik">Nomor kuitansi fisik <span class="opsional">(opsional)</span></label>
            <input id="ek-fisik" name="nomor_kuitansi_fisik" type="text" class="inp" maxlength="50" value="{{ $nilai('modal-edit-kuitansi', 'nomor_kuitansi_fisik', $kuitansi->nomor_kuitansi_fisik) }}">
            @if ($m = $galat('modal-edit-kuitansi', 'nomor_kuitansi_fisik'))<div class="err">{{ $m }}</div>@endif
        </div>
        <div class="fld">
            <label for="ek-catatan">Catatan</label>
            <textarea id="ek-catatan" name="catatan" rows="3" class="inp" maxlength="500">{{ $nilai('modal-edit-kuitansi', 'catatan', $kuitansi->keterangan) }}</textarea>
            @if ($m = $galat('modal-edit-kuitansi', 'catatan'))<div class="err">{{ $m }}</div>@endif
        </div>
        <div class="fld">
            <label for="ek-foto">Ganti foto kuitansi</label>
            <input id="ek-foto" name="bukti" type="file" accept="image/jpeg,image/png" class="inp">
            <div class="hlp">{{ $bukti['has_bukti'] ? 'Foto saat ini dipertahankan bila tidak memilih file. ' : '' }}JPG atau PNG, maksimal 10 MB.</div>
            @if ($m = $galat('modal-edit-kuitansi', 'bukti'))<div class="err">{{ $m }}</div>@endif
        </div>
        <div class="modal-foot">
            <button type="button" class="btn-soft" data-tutup>Batal</button>
            <button type="submit" class="btn-primary kecil">Simpan perubahan</button>
        </div>
    </form>
</dialog>

@foreach ($semuaPembayaran->where('status', 'aktif') as $bayar)
    @php
        $idEdit = 'modal-edit-bayar-'.$bayar->id;
        $idBatal = 'modal-batal-bayar-'.$bayar->id;
        $metodeIni = (string) $bayar->metode_pembayaran;
        // Data lama ("Transfer", "Tunai - DP") disatukan ke dua pilihan baku; nilai tak dikenal tetap dipertahankan.
        $metodeAwal = match (\App\Models\Pembayaran::labelMetode($metodeIni)) {
            'Tunai' => 'Tunai',
            'Transfer' => 'Transfer bank',
            default => $metodeIni,
        };
        $opsiMetode = array_values(array_unique(array_filter(['Tunai', 'Transfer bank', $metodeAwal])));
    @endphp
    <dialog class="modal" id="{{ $idEdit }}" aria-labelledby="jdl-{{ $idEdit }}">
        <form method="POST" action="{{ route('arsip-kuitansi.pembayaran.update', [$kuitansi, $bayar]) }}" class="modal-box">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="{{ $idEdit }}">
            <div class="modal-head">
                <div>
                    <h3 id="jdl-{{ $idEdit }}">Edit pembayaran</h3>
                    <p>Koreksi data pembayaran. Alasan koreksi akan tercatat di riwayat.</p>
                </div>
                <button type="button" class="modal-x" data-tutup aria-label="Tutup">×</button>
            </div>
            <div class="grid2">
                <div class="fld">
                    <label for="{{ $idEdit }}-tgl">Tanggal pembayaran</label>
                    <input id="{{ $idEdit }}-tgl" name="tanggal_pembayaran" type="date" class="inp" max="{{ now()->toDateString() }}" value="{{ $nilai($idEdit, 'tanggal_pembayaran', $bayar->tanggal_pembayaran->toDateString()) }}" required>
                    @if ($m = $galat($idEdit, 'tanggal_pembayaran'))<div class="err">{{ $m }}</div>@endif
                </div>
                <div class="fld">
                    <label for="{{ $idEdit }}-jml">Jumlah (Rp)</label>
                    <input id="{{ $idEdit }}-jml" name="nominal" type="number" min="1" step="1" class="inp" value="{{ $nilai($idEdit, 'nominal', (int) $bayar->nominal) }}" required>
                    @if ($m = $galat($idEdit, 'nominal'))<div class="err">{{ $m }}</div>@endif
                </div>
            </div>
            <div class="fld">
                <label for="{{ $idEdit }}-metode">Metode</label>
                <select id="{{ $idEdit }}-metode" name="metode_pembayaran" class="inp">
                    @foreach ($opsiMetode as $metode)
                        <option value="{{ $metode }}" @selected($nilai($idEdit, 'metode_pembayaran', $metodeAwal) === $metode)>{{ \App\Models\Pembayaran::labelMetode($metode) }}</option>
                    @endforeach
                </select>
                @if ($m = $galat($idEdit, 'metode_pembayaran'))<div class="err">{{ $m }}</div>@endif
            </div>
            <div class="fld">
                <label for="{{ $idEdit }}-alasan">Alasan koreksi *</label>
                <textarea id="{{ $idEdit }}-alasan" name="alasan_koreksi" rows="3" class="inp" maxlength="255" placeholder="Contoh: salah input nominal" required>{{ $nilai($idEdit, 'alasan_koreksi') }}</textarea>
                @if ($m = $galat($idEdit, 'alasan_koreksi'))<div class="err">{{ $m }}</div>@endif
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-soft" data-tutup>Batal</button>
                <button type="submit" class="btn-primary kecil">Simpan koreksi</button>
            </div>
        </form>
    </dialog>

    <dialog class="modal" id="{{ $idBatal }}" aria-labelledby="jdl-{{ $idBatal }}">
        <form method="POST" action="{{ route('arsip-kuitansi.pembayaran.batalkan', [$kuitansi, $bayar]) }}" class="modal-box">
            @csrf
            <input type="hidden" name="_modal" value="{{ $idBatal }}">
            <div class="modal-head">
                <div>
                    <h3 id="jdl-{{ $idBatal }}">Batalkan pembayaran</h3>
                    <p>Pembayaran {{ $rp($bayar->nominal) }} akan ditandai Dibatalkan.</p>
                </div>
                <button type="button" class="modal-x" data-tutup aria-label="Tutup">×</button>
            </div>
            <div class="fld">
                <label for="{{ $idBatal }}-alasan">Alasan pembatalan *</label>
                <textarea id="{{ $idBatal }}-alasan" name="alasan_pembatalan" rows="3" class="inp" maxlength="255" placeholder="Contoh: pembayaran tercatat dua kali" required>{{ $nilai($idBatal, 'alasan_pembatalan') }}</textarea>
                @if ($m = $galat($idBatal, 'alasan_pembatalan'))<div class="err">{{ $m }}</div>@endif
            </div>
            <div class="peringatan">Pembayaran tidak dihapus. Pembayaran ditandai Dibatalkan dan sisa tagihan dihitung ulang.</div>
            <div class="modal-foot">
                <button type="button" class="btn-soft" data-tutup>Kembali</button>
                <button type="submit" class="btn-soft bahaya">Batalkan pembayaran</button>
            </div>
        </form>
    </dialog>
@endforeach

<dialog class="modal" id="modal-batal-transaksi" aria-labelledby="jdl-batal-transaksi">
    <form method="POST" action="{{ route('arsip-kuitansi.batalkan', $kuitansi) }}" class="modal-box">
        @csrf
        <input type="hidden" name="_modal" value="modal-batal-transaksi">
        <div class="modal-head">
            <div>
                <h3 id="jdl-batal-transaksi">Batalkan transaksi</h3>
                <p>Transaksi {{ $kuitansi->nomor_kuitansi }} dibatalkan dan penyelesaian uang dicatat.</p>
            </div>
            <button type="button" class="modal-x" data-tutup aria-label="Tutup">×</button>
        </div>
        <div class="info">Uang yang sudah tercatat pada transaksi ini: <b>{{ $rp($terbayar) }}</b></div>
        @if ($terbayar > 0)
            @php $pilihan = $nilai('modal-batal-transaksi', 'penyelesaian_uang', 'dikembalikan'); @endphp
            <div class="fld">
                <span class="label-teks">Penyelesaian uang</span>
                <div class="chips">
                    <label class="chip"><input type="radio" name="penyelesaian_uang" value="dikembalikan" @checked($pilihan === 'dikembalikan')><span>Uang dikembalikan penuh</span></label>
                    <label class="chip"><input type="radio" name="penyelesaian_uang" value="tidak_dikembalikan" @checked($pilihan === 'tidak_dikembalikan')><span>Uang tidak dikembalikan</span></label>
                </div>
                @if ($m = $galat('modal-batal-transaksi', 'penyelesaian_uang'))<div class="err">{{ $m }}</div>@endif
            </div>
        @endif
        <div class="fld">
            <label for="bt-alasan">Alasan pembatalan *</label>
            <textarea id="bt-alasan" name="alasan_pembatalan" rows="3" class="inp" maxlength="500" required>{{ $nilai('modal-batal-transaksi', 'alasan_pembatalan') }}</textarea>
            @if ($m = $galat('modal-batal-transaksi', 'alasan_pembatalan'))<div class="err">{{ $m }}</div>@endif
        </div>
        <div class="modal-foot">
            <button type="button" class="btn-soft" data-tutup>Kembali</button>
            <button type="submit" class="btn-soft bahaya">Batalkan transaksi</button>
        </div>
    </form>
</dialog>
@endunless

<script>
    (function () {
        document.querySelectorAll('[data-buka]').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                var dlg = document.getElementById(tombol.dataset.buka);
                if (dlg && typeof dlg.showModal === 'function') { dlg.showModal(); }
            });
        });

        document.querySelectorAll('dialog.modal').forEach(function (dlg) {
            // Klik di luar kotak (pada latar gelap) menutup modal.
            dlg.addEventListener('click', function (e) { if (e.target === dlg) { dlg.close(); } });
            dlg.querySelectorAll('[data-tutup]').forEach(function (b) {
                b.addEventListener('click', function () { dlg.close(); });
            });
            // Cegah kirim ganda.
            var form = dlg.querySelector('form');
            if (form) {
                form.addEventListener('submit', function () {
                    form.querySelectorAll('button[type=submit]').forEach(function (b) { b.disabled = true; });
                });
            }
        });

        // Ringkasan di modal Tambah pembayaran.
        var formTambah = document.querySelector('#modal-tambah form');
        if (formTambah) {
            var rp = function (n) { return 'Rp' + Math.max(0, Math.round(n)).toLocaleString('id-ID'); };
            var sisa = parseFloat(formTambah.dataset.sisa) || 0;
            var input = formTambah.querySelector('[name=nominal]');
            var kotak = formTambah.querySelector('[data-ringkasan]');
            var hitung = function () {
                var tambahan = parseFloat(input.value) || 0;
                kotak.textContent = 'Sisa saat ini ' + rp(sisa) + '. Setelah disimpan: ' + rp(sisa - tambahan) + '.';
            };
            input.addEventListener('input', hitung);
            hitung();
        }

        // Buka lagi modal yang gagal divalidasi supaya galatnya terlihat.
        var terbuka = @json($modalAktif);
        if (terbuka) {
            var dlg = document.getElementById(terbuka);
            if (dlg && typeof dlg.showModal === 'function') { dlg.showModal(); }
        }
    })();
</script>
</body>
</html>