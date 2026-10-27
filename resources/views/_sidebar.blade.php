<style>
    /* =======================================================
       UNIFIED SIDEBAR MENU & DROPDOWN COLLAPSIBLE STYLES
       ======================================================= */
    .sidebar .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin: 0;
        padding: 0;
    }

    /* All Main Menu Items & Dashboard Header: Exactly Identical 40px Height & 8px Radius */
    .sidebar .sidebar-menu > a,
    .sidebar .sidebar-menu .menu-dropdown-header {
        height: 40px !important;
        min-height: 40px !important;
        max-height: 40px !important;
        border-radius: 8px !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        transition: background 0.15s ease, color 0.15s ease;
    }

    /* Direct Menu Links (Arsip Kuitansi, Kuitansi Baru, Rekapitulasi) */
    .sidebar .sidebar-menu > a {
        padding: 0 12px !important;
        gap: 10px;
        color: #fce7eb;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        background: transparent;
    }

    .sidebar .sidebar-menu > a:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff;
    }

    .sidebar .sidebar-menu > a.menu-active {
        background: rgba(255, 255, 255, 0.16) !important;
        color: #ffffff;
        font-weight: 600;
    }

    /* Dropdown Group Container */
    .sidebar .sidebar-menu .menu-dropdown {
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 0;
    }

    /* Dashboard Header Container */
    .sidebar .sidebar-menu .menu-dropdown-header {
        justify-content: space-between;
        padding: 0 8px 0 12px !important;
        background: transparent;
        cursor: pointer;
    }

    .sidebar .sidebar-menu .menu-dropdown-header:hover {
        background: rgba(255, 255, 255, 0.1) !important;
    }

    .sidebar .sidebar-menu .menu-dropdown-header.menu-active {
        background: rgba(255, 255, 255, 0.16) !important;
    }

    /* Dashboard Link inside Header */
    .sidebar .sidebar-menu .menu-dropdown-link {
        display: flex;
        align-items: center;
        gap: 10px;
        height: 100%;
        flex: 1;
        padding: 0 !important;
        margin: 0 !important;
        color: #fce7eb;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        background: transparent !important;
        border: none !important;
    }

    .sidebar .sidebar-menu .menu-dropdown-header:hover .menu-dropdown-link,
    .sidebar .sidebar-menu .menu-dropdown-header.menu-active .menu-dropdown-link {
        color: #ffffff;
    }

    .sidebar .sidebar-menu .menu-dropdown-header.menu-active .menu-dropdown-link {
        font-weight: 600;
    }

    /* Minimal, Clean Toggle Button (Fits seamlessly in 40px bar) */
    .sidebar .sidebar-menu .menu-toggle-btn {
        width: 26px;
        height: 26px;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        border: none !important;
        color: #fce7eb;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background 0.15s ease, color 0.15s ease;
        flex-shrink: 0;
    }

    .sidebar .sidebar-menu .menu-toggle-btn:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15) !important;
    }

    .sidebar .sidebar-menu .chevron-arrow {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar .sidebar-menu .menu-dropdown.is-open .chevron-arrow {
        transform: rotate(180deg);
    }

    /* Ultra-Smooth Expand / Collapse Submenu using CSS Grid Rows */
    .sidebar .sidebar-menu .menu-dropdown-wrapper {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar .sidebar-menu .menu-dropdown.is-open .menu-dropdown-wrapper {
        grid-template-rows: 1fr;
    }

    .sidebar .sidebar-menu .menu-dropdown-inner {
        overflow: hidden;
        padding-left: 18px;
        opacity: 0;
        transform: translateY(-4px);
        transition: opacity 0.25s ease, transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar .sidebar-menu .menu-dropdown.is-open .menu-dropdown-inner {
        opacity: 1;
        transform: translateY(0);
        padding-top: 3px;
        padding-bottom: 2px;
    }

    /* Submenu Link (Utang & Piutang) */
    .sidebar .sidebar-menu .menu-sub-link {
        display: flex;
        align-items: center;
        height: 34px !important;
        min-height: 34px !important;
        max-height: 34px !important;
        padding: 0 12px !important;
        font-size: 12.5px;
        color: #fce7eb;
        text-decoration: none;
        border-radius: 8px !important;
        transition: background 0.15s ease, color 0.15s ease;
        background: transparent;
        box-sizing: border-box !important;
    }

    .sidebar .sidebar-menu .menu-sub-link:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1) !important;
    }

    .sidebar .sidebar-menu .menu-sub-link.sub-active {
        color: #ffffff;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.16) !important;
    }
</style>

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
        <!-- Dashboard & Collapsible Submenu Utang Piutang -->
        <div class="menu-dropdown {{ request()->routeIs('dashboard', 'jatuh-tempo.*') ? 'is-open' : '' }}" id="dashboardMenuDropdown">
            <div class="menu-dropdown-header {{ request()->routeIs('dashboard') ? 'menu-active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-dropdown-link" title="Dashboard">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <button type="button" class="menu-toggle-btn" onclick="toggleDashboardSubmenu(event)" title="Sembunyikan / Tampilkan menu Utang & Piutang">
                    <svg class="chevron-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>
            <div class="menu-dropdown-wrapper" id="utangPiutangSubmenu">
                <div class="menu-dropdown-inner">
                    <a href="{{ route('jatuh-tempo.index') }}" class="menu-sub-link {{ request()->routeIs('jatuh-tempo.*') ? 'sub-active' : '' }}">
                        • Utang & Piutang
                    </a>
                </div>
            </div>
        </div>

        <a href="{{ route('arsip-kuitansi.index') }}" class="{{ request()->routeIs('arsip-kuitansi.*') ? 'menu-active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="21 8 21 21 3 21 3 8"></polyline>
                <rect x="1" y="3" width="22" height="5"></rect>
                <line x1="10" y1="12" x2="14" y2="12"></line>
            </svg>
            Arsip Kuitansi
        </a>

        <a href="{{ route('kuitansi.create') }}" class="{{ request()->routeIs('kuitansi.*') ? 'menu-active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="18" x2="12" y2="12"></line>
                <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
            Kuitansi Baru
        </a>

        <a href="{{ route('rekapitulasi.index') }}" class="{{ request()->routeIs('rekapitulasi.*') ? 'menu-active' : '' }}">
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
        <div class="sync-info-time">{{ (isset($tanggalAcuan) ? $tanggalAcuan : now())->translatedFormat('d M Y') }}, {{ now()->format('H.i') }}</div>
    </div>

    <!-- User Profile Bar -->
    <div class="sidebar-user">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AS', 0, 2)) }}</div>
        <div class="sidebar-user-info">
            <div class="user-name">{{ auth()->user()->name ?? 'Andi Saputra' }}</div>
            <div class="user-role">Owner • Toko Tempo</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0; background: none; border: none; box-shadow: none;">
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

<script>
    function toggleDashboardSubmenu(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const dropdown = document.getElementById('dashboardMenuDropdown');
        if (dropdown) {
            const isOpen = dropdown.classList.toggle('is-open');
            try {
                localStorage.setItem('sidebar_submenu_dashboard', isOpen ? 'open' : 'closed');
            } catch (err) {}
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.getElementById('dashboardMenuDropdown');
        if (dropdown) {
            const isJatuhTempo = {{ request()->routeIs('jatuh-tempo.*') ? 'true' : 'false' }};
            const isDashboard = {{ request()->routeIs('dashboard') ? 'true' : 'false' }};
            try {
                const savedState = localStorage.getItem('sidebar_submenu_dashboard');
                if (isJatuhTempo) {
                    dropdown.classList.add('is-open');
                } else if (savedState === 'open') {
                    dropdown.classList.add('is-open');
                } else if (savedState === 'closed') {
                    dropdown.classList.remove('is-open');
                } else if (isDashboard) {
                    dropdown.classList.add('is-open');
                } else {
                    dropdown.classList.remove('is-open');
                }
            } catch (err) {
                if (isJatuhTempo || isDashboard) {
                    dropdown.classList.add('is-open');
                }
            }
        }
    });
</script>
