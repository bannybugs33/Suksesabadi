    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </div>
            <div class="brand-text">
                BukuTempo
                <small>CATAT • INGAT • LUNAS</small>
            </div>
        </div>

        <p class="menu-label">MENU UTAMA</p>

        <nav class="sidebar-menu" aria-label="Menu utama">
            <div class="menu-group-active">
                <a href="{{ route('dashboard') }}" class="menu-parent-link" style="color: #fce7eb; font-weight: 500;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('jatuh-tempo.index') }}" class="menu-sub-link">
                    • Utang & Piutang
                </a>
            </div>

            <a href="{{ route('arsip-kuitansi.index') }}" class="menu-active">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="21 8 21 21 3 21 3 8"></polyline>
                    <rect x="1" y="3" width="22" height="5"></rect>
                    <line x1="10" y1="12" x2="14" y2="12"></line>
                </svg>
                Arsip Kuitansi
            </a>
            <a href="{{ route('dashboard') }}" class="menu-disabled" style="cursor: pointer;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="12" y1="18" x2="12" y2="12"></line>
                    <line x1="9" y1="15" x2="15" y2="15"></line>
                </svg>
                Kuitansi Baru
            </a>
            <a href="{{ route('rekapitulasi.index') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                Rekapitulasi
            </a>
        </nav>

        <div class="sidebar-spacer"></div>

        <!-- Sync Info Box -->
        <div class="sync-info-box">
            <div class="sync-info-status">
                <span class="sync-dot"></span>
                <span>Data tersinkron</span>
            </div>
            <div class="sync-info-label">Terakhir diperbarui</div>
            <div class="sync-info-time">{{ now()->translatedFormat('d M Y') }}, {{ now()->format('H.i') }}</div>
        </div>

        <!-- User Profile Bar -->
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AS', 0, 2)) }}</div>
            <div class="sidebar-user-info">
                <div class="user-name">{{ auth()->user()->name ?? 'Andi Saputra' }}</div>
                <div class="user-role">Owner • Toko Tempo</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0;">
                @csrf
                <button type="submit" class="logout-icon-btn" title="Keluar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </button>
            </form>
        </div>
    </aside>
