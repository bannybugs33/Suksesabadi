{{--
    Navigasi atas. Parameter:
      $aktifMenu      : 'dashboard' | 'arsip' | 'kuitansi-baru' | 'jatuh-tempo' | 'rekapitulasi'
      $badgePengingat : jumlah tagihan yang perlu perhatian (H-7 dan terlambat); opsional
    Setiap menu mengarah ke fitur yang sesuai.
--}}
@php
    $menu = [
        'dashboard' => ['Dashboard', route('dashboard')],
        'arsip' => ['Arsip', route('arsip-kuitansi.index')],
        'kuitansi-baru' => ['Kuitansi baru', route('kuitansi.create')],
        'jatuh-tempo' => ['Jatuh tempo', route('jatuh-tempo.index')],
        'rekapitulasi' => ['Rekapitulasi', route('rekapitulasi.index')],
    ];
    $badgePengingat = (int) ($badgePengingat ?? 0);
    $namaPengguna = auth()->user()->name ?? 'Owner';
    $kata = preg_split('/\s+/', trim($namaPengguna)) ?: [];
    $inisial = count($kata) > 1
        ? mb_substr($kata[0], 0, 1).mb_substr($kata[1], 0, 1)
        : mb_substr($kata[0] ?? 'O', 0, 2);
    $inisial = mb_strtoupper($inisial);
@endphp
<header class="topnav">
    <a href="{{ route('dashboard') }}" class="topnav-brand" aria-label="BukuTempo, ke Dashboard">
        <span class="topnav-mark" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2h12a1 1 0 0 1 1 1v18l-3-2-3 2-3-2-3 2V3a1 1 0 0 1 1-1z"></path>
                <line x1="9" y1="8" x2="15" y2="8"></line>
                <line x1="9" y1="12" x2="15" y2="12"></line>
            </svg>
        </span>
        <span>
            <span class="topnav-name">BukuTempo</span>
            <span class="topnav-tag">CATAT • INGAT • LUNAS</span>
        </span>
    </a>

    <nav class="topnav-links" aria-label="Menu utama">
        @foreach ($menu as $kunci => [$label, $url])
            <a href="{{ $url }}" @class(['is-active' => $aktifMenu === $kunci]) @if ($aktifMenu === $kunci) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>

    <div class="topnav-actions">
        <a href="{{ route('jatuh-tempo.index') }}" class="bell" aria-label="Pengingat jatuh tempo: {{ $badgePengingat }} tagihan perlu perhatian" title="Pengingat jatuh tempo">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            @if ($badgePengingat > 0)
                <span class="bell-badge">{{ $badgePengingat > 9 ? '9+' : $badgePengingat }}</span>
            @endif
        </a>

        <details class="akun">
            <summary aria-label="Menu akun {{ $namaPengguna }}"><span class="avatar">{{ $inisial }}</span></summary>
            <div class="akun-menu">
                <strong>{{ $namaPengguna }}</strong>
                <span>Owner • Toko Sukses Abadi</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </details>
    </div>
</header>
<script>
    document.addEventListener('click', function (e) {
        var akun = document.querySelector('.akun');
        if (akun && akun.open && !akun.contains(e.target)) { akun.open = false; }
    });
</script>
