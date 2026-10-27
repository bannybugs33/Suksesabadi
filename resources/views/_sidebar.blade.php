<style>
    /* Menu Dropdown Collapsible */
    .menu-dropdown {
        display: flex;
        flex-direction: column;
        gap: 2px;
        border-radius: 8px;
    }

    .menu-dropdown-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 8px;
        transition: all 0.15s ease;
    }

    .menu-dropdown-header:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .menu-dropdown-header.menu-active {
        background: rgba(255, 255, 255, 0.16);
    }

    .sidebar-menu .menu-dropdown-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        color: #fce7eb;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        flex: 1;
        border-radius: 8px;
        transition: color 0.15s ease;
        background: transparent !important;
    }

    .menu-dropdown-header:hover .menu-dropdown-link,
    .menu-dropdown-header.menu-active .menu-dropdown-link {
        color: #ffffff;
    }

    .menu-dropdown-header.menu-active .menu-dropdown-link {
        font-weight: 600;
    }

    .menu-toggle-btn {
        background: none;
        border: none;
        padding: 10px 12px;
        color: #fce7eb;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.15s ease;
    }

    .menu-toggle-btn:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.12);
    }

    .chevron-arrow {
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .menu-dropdown.is-open .chevron-arrow {
        transform: rotate(180deg);
    }

    .menu-dropdown-content {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.25s ease-out, opacity 0.2s ease-out;
        padding-left: 24px;
    }

    .menu-dropdown.is-open .menu-dropdown-content {
        max-height: 80px;
        opacity: 1;
        padding-top: 2px;
        padding-bottom: 2px;
    }

    .sidebar-menu .menu-sub-link {
        display: block;
        padding: 6px 12px 6px 14px;
        font-size: 12px;
        color: #fce7eb;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.15s ease;
        background: transparent;
    }

    .sidebar-menu .menu-sub-link:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1) !important;
    }

    .sidebar-menu .menu-sub-link.sub-active {
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
            <div class="menu-dropdown-content" id="utangPiutangSubmenu">
                <a href="{{ route('jatuh-tempo.index') }}" class="menu-sub-link {{ request()->routeIs('jatuh-tempo.*') ? 'sub-active' : '' }}">
                    • Utang & Piutang
                </a>
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
