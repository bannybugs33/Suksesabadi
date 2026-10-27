<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Dashboard — BukuTempo</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f7f8fa;
            color: #1e2029;
            font-family: Arial, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 220px;
            padding: 24px 16px;
            background: #91283b;
            color: white;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .brand {
            padding: 0 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-text {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.1;
        }

        .brand-text small {
            display: block;
            margin-top: 3px;
            font-size: 7.5px;
            font-weight: 600;
            letter-spacing: 1.5px;
            color: #fce7eb;
        }

        .menu-label {
            margin: 28px 10px 10px;
            font-size: 10px;
            color: #f0c7cf;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-menu a,
        .sidebar-menu span {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: #fce7eb;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu a.menu-active {
            background: rgba(255, 255, 255, 0.16);
            color: white;
            font-weight: 600;
        }

        .sidebar-menu .menu-disabled {
            opacity: 0.85;
            cursor: pointer;
        }

        .menu-group-active {
            background: rgba(255, 255, 255, 0.16);
            border-radius: 8px;
            padding-bottom: 6px;
        }

        .menu-parent-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px 4px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .menu-sub-link {
            display: block;
            padding: 2px 12px 4px 38px;
            font-size: 12px;
            color: #fce7eb;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .menu-sub-link:hover,
        .menu-sub-link.active {
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar-spacer {
            flex: 1;
            min-height: 24px;
        }

        /* Sidebar Sync Info Box */
        .sync-info-box {
            background: rgba(0, 0, 0, 0.18);
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        .sync-info-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #f8d5dc;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .sync-dot {
            width: 7px;
            height: 7px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
        }

        .sync-info-label {
            font-size: 10px;
            color: #e8a2af;
        }

        .sync-info-time {
            font-size: 11px;
            color: #ffffff;
            font-weight: 600;
            margin-top: 1px;
        }

        /* Sidebar User Profile */
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 6px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #ffffff;
        }

        .user-role {
            font-size: 10.5px;
            color: #f0c7cf;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-icon-btn {
            background: none;
            border: none;
            color: #fce7eb;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .logout-icon-btn:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        /* Main Content */
        .main-content {
            margin-left: 220px;
            padding: 32px 36px;
            max-width: 1400px;
        }

        /* Top Header */
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .header-title-wrap h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a24;
            letter-spacing: -0.3px;
        }

        .header-title-wrap p {
            color: #696b78;
            font-size: 13px;
            margin-top: 4px;
        }

        .btn-tambah-kuitansi {
            background: #91283b;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 6px rgba(145, 40, 59, 0.2);
            transition: background 0.15s ease;
            text-decoration: none;
        }

        .btn-tambah-kuitansi:hover {
            background: #7a2232;
        }

        /* Top Warning Banner: Peringatan Jatuh Tempo */
        .warning-banner {
            background: #fdf2f4;
            border: 1px solid #fee2e2;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .warning-banner-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .warning-icon-box {
            width: 40px;
            height: 40px;
            background: #91283b;
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .warning-banner-left h2 {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a24;
            margin-bottom: 2px;
        }

        .warning-banner-left p {
            font-size: 12px;
            color: #696b78;
        }

        .btn-lihat-detail {
            background: #ffffff;
            border: 1px solid #e2e4ea;
            border-radius: 8px;
            padding: 7px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-lihat-detail:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        /* 4 Summary Cards Grid */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-card {
            background: #ffffff;
            border: 1px solid #edf0f4;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .summary-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .summary-card-title {
            font-size: 12px;
            font-weight: 500;
            color: #696b78;
        }

        .summary-icon-box {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .summary-icon-box.gray {
            background: #f3f4f6;
            color: #4b5563;
        }

        .summary-icon-box.red {
            background: #fdf2f4;
            color: #dc2626;
        }

        .summary-icon-box.amber {
            background: #fef3c7;
            color: #d97706;
        }

        .summary-icon-box.rose {
            background: #fdf2f4;
            color: #91283b;
        }

        .summary-card-value {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a24;
            margin: 12px 0 6px;
            letter-spacing: -0.3px;
        }

        .summary-card-subtext {
            font-size: 11px;
            color: #696b78;
        }

        .summary-card-subtext.red {
            color: #dc2626;
            font-weight: 500;
        }

        .summary-card-subtext.amber {
            color: #b45309;
            font-weight: 500;
        }

        /* 2-Column Layout for Middle & Bottom Rows */
        .content-grid-row {
            display: grid;
            grid-template-columns: 1.75fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .card-box {
            background: #ffffff;
            border: 1px solid #edf0f4;
            border-radius: 12px;
            padding: 22px 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }

        .card-box-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .card-box-title {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a24;
        }

        .card-box-link {
            font-size: 12px;
            font-weight: 600;
            color: #91283b;
            text-decoration: none;
        }

        .card-box-link:hover {
            text-decoration: underline;
        }

        /* Arus Utang & Piutang Chart Card */
        .chart-legend-row {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #696b78;
        }

        .legend-color-box {
            width: 10px;
            height: 10px;
            border-radius: 2px;
        }

        .legend-color-box.gray {
            background: #646675;
        }

        .legend-color-box.red {
            background: #91283b;
        }

        /* Bar Chart Visual */
        .bar-chart-container {
            height: 180px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding-bottom: 28px;
            border-bottom: 1px solid #edf0f4;
            position: relative;
            margin-top: 10px;
        }

        .chart-month-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            justify-content: flex-end;
            position: relative;
        }

        .chart-bars-wrap {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 100%;
        }

        .bar-item {
            width: 24px;
            border-radius: 4px 4px 0 0;
            transition: opacity 0.15s ease;
        }

        .bar-item:hover {
            opacity: 0.85;
        }

        .bar-item.gray {
            background: #646675;
        }

        .bar-item.red {
            background: #91283b;
        }

        .month-label {
            position: absolute;
            bottom: -24px;
            font-size: 11px;
            color: #8b8e9c;
            font-weight: 500;
        }

        /* Floating Popup Over Chart: Detail Pengingat */
        .chart-overlay-popup {
            position: absolute;
            top: 24px;
            right: 48px;
            width: 275px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            z-index: 10;
        }

        .popup-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .popup-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a24;
        }

        .popup-subtitle {
            font-size: 11px;
            color: #696b78;
            margin-top: 1px;
        }

        .popup-close-btn {
            background: none;
            border: none;
            color: #8b8e9c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 4px;
            cursor: pointer;
            padding: 0;
            transition: all 0.15s ease;
        }

        .popup-close-btn:hover {
            color: #1a1a24;
            background: rgba(0, 0, 0, 0.05);
        }

        .popup-item-box {
            background: #fdf2f4;
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .popup-item-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .popup-item-icon {
            width: 28px;
            height: 28px;
            background: #91283b;
            color: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .popup-item-title {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a24;
            white-space: nowrap;
        }

        .popup-item-meta {
            font-size: 10.5px;
            color: #696b78;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .popup-item-amount {
            font-size: 12px;
            font-weight: 700;
            color: #dc2626;
            white-space: nowrap;
        }

        .btn-popup-action {
            display: block;
            width: 100%;
            background: #91283b;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s ease;
        }

        .btn-popup-action:hover {
            background: #7a2232;
        }

        /* Status Pembayaran Progress Bars */
        .status-progress-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 8px;
        }

        .status-progress-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .status-row-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
        }

        .status-name {
            font-weight: 600;
            color: #1a1a24;
        }

        .status-count-pct {
            color: #696b78;
            font-size: 11px;
        }

        .progress-track {
            width: 100%;
            height: 6px;
            background: #f1f3f7;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
        }

        .status-footer-row {
            margin-top: 24px;
            padding-top: 14px;
            border-top: 1px solid #edf0f4;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-footer-label {
            font-size: 11.5px;
            color: #696b78;
        }

        .status-footer-value {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a24;
        }

        /* Perlu Perhatian List */
        .perhatian-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .perhatian-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s ease;
        }

        .perhatian-item:hover {
            background: #fcf5f6;
        }

        .perhatian-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .perhatian-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .perhatian-icon-box.red {
            background: #fdf2f4;
            color: #dc2626;
        }

        .perhatian-icon-box.amber {
            background: #fef3c7;
            color: #b45309;
        }

        .perhatian-party {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a24;
        }

        .perhatian-meta {
            font-size: 11px;
            color: #696b78;
            margin-top: 2px;
        }

        .perhatian-right {
            text-align: right;
            flex-shrink: 0;
        }

        .perhatian-nominal {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a24;
        }

        .perhatian-tempo {
            font-size: 11px;
            margin-top: 2px;
            font-weight: 500;
        }

        .perhatian-tempo.red {
            color: #dc2626;
        }

        .perhatian-tempo.amber {
            color: #b45309;
        }

        /* Aktivitas Terbaru List */
        .aktivitas-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-top: 6px;
        }

        .aktivitas-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .aktivitas-icon-box {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .aktivitas-content {
            flex: 1;
            min-width: 0;
        }

        .aktivitas-title {
            font-size: 12.5px;
            font-weight: 600;
            color: #1a1a24;
            line-height: 1.3;
        }

        .aktivitas-meta {
            font-size: 11px;
            color: #696b78;
            margin-top: 2px;
        }

        .aktivitas-time {
            font-size: 10.5px;
            color: #9ca3af;
            margin-top: 4px;
        }

        /* Modal Tambah Kuitansi */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 200;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-card {
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 100%;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a24;
        }

        .modal-close-btn {
            background: none;
            border: none;
            color: #8b8e9c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            cursor: pointer;
            padding: 0;
            transition: all 0.15s ease;
        }

        .modal-close-btn:hover {
            color: #1a1a24;
            background: #f3f4f6;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1120px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .content-grid-row {
                grid-template-columns: 1fr;
            }
            .chart-overlay-popup {
                position: static;
                width: 100%;
                margin-top: 20px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                width: 100%;
                padding: 16px;
            }
            .main-content {
                margin-left: 0;
                padding: 20px 16px;
            }
            .summary-grid {
                grid-template-columns: 1fr;
            }
        .modal-custom-select {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            height: 38px;
            padding: 8px 12px;
            border: 1px solid #d8dbe2;
            border-radius: 8px;
            font-size: 13px;
            background: #ffffff;
            color: #1a1a24;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }

        .modal-custom-select:hover {
            border-color: #b0b4be;
            background: #fafafc;
        }

        .modal-custom-select.is-open {
            border-color: #91283b;
            box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.12);
        }

        .dropdown-chevron {
            color: #717381;
            transition: transform 0.2s ease, color 0.15s ease;
            flex-shrink: 0;
        }

        .modal-custom-select.is-open .dropdown-chevron {
            transform: rotate(180deg);
            color: #91283b;
        }

        .custom-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            width: 100%;
            background: #ffffff;
            border: 1px solid #e6e7ec;
            border-radius: 12px;
            box-shadow: 0 14px 34px -4px rgba(26, 26, 36, 0.14), 0 4px 12px -2px rgba(26, 26, 36, 0.05);
            padding: 6px;
            z-index: 200;
            box-sizing: border-box;
            animation: dropdownFadeIn 0.16s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-custom-select.is-open .custom-dropdown-menu {
            display: block;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-6px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .custom-dropdown-item {
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #292930;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
            transition: all 0.12s ease;
        }

        .custom-dropdown-item:hover {
            background: #fdf2f4;
            color: #91283b;
        }

        .custom-dropdown-item.is-selected {
            background: #fbf2f4;
            color: #91283b;
            font-weight: 600;
        }

        .dropdown-check {
            color: #91283b;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    @include('_sidebar')

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Dashboard Top Header -->
        <header class="dashboard-header">
            <div class="header-title-wrap">
                <h1>Selamat pagi, Pak {{ explode(' ', auth()->user()->name ?? 'Andi')[0] }}</h1>
                <p>Berikut kondisi utang–piutang Toko Tempo per {{ $tanggalAcuan->translatedFormat('d F Y') }}.</p>
            </div>
            <a href="{{ route('kuitansi.create') }}" class="btn-tambah-kuitansi" style="text-decoration: none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah kuitansi
            </a>
        </header>

        <!-- Peringatan Jatuh Tempo Banner -->
        <div class="warning-banner">
            <div class="warning-banner-left">
                <div class="warning-icon-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <h2>Peringatan jatuh tempo</h2>
                    <p>Pengingat aktif mulai H-3. Ada {{ $perluPerhatianCount }} utang dan piutang yang perlu perhatian hari ini.</p>
                </div>
            </div>
            <a href="{{ route('jatuh-tempo.index') }}" class="btn-lihat-detail">Lihat detail</a>
        </div>

        <!-- 4 Summary Metric Cards -->
        <div class="summary-grid">
            <!-- Card 1: Sisa Piutang Pelanggan -->
            <div class="summary-card">
                <div class="summary-card-header">
                    <span class="summary-card-title">Sisa piutang pelanggan</span>
                    <div class="summary-icon-box gray">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="17" y1="7" x2="7" y2="17"></line>
                            <polyline points="17 17 7 17 7 7"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="summary-card-value">Rp{{ number_format($sisaPiutangPelanggan, 0, ',', '.') }}</div>
                <div class="summary-card-subtext">{{ $kuitansiPelangganCount }} kuitansi pelanggan</div>
            </div>

            <!-- Card 2: Sisa Utang Supplier -->
            <div class="summary-card">
                <div class="summary-card-header">
                    <span class="summary-card-title">Sisa utang supplier</span>
                    <div class="summary-icon-box red">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="summary-card-value">Rp{{ number_format($sisaUtangSupplier, 0, ',', '.') }}</div>
                <div class="summary-card-subtext red">{{ $kuitansiSupplierCount }} kuitansi supplier</div>
            </div>

            <!-- Card 3: Jatuh Tempo -->
            <div class="summary-card">
                <div class="summary-card-header">
                    <span class="summary-card-title">Jatuh tempo</span>
                    <div class="summary-icon-box amber">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="13" r="8"></circle>
                            <path d="M12 9v4l2 2"></path>
                            <path d="M5 3L2 6"></path>
                            <path d="M22 6l-3-3"></path>
                            <path d="M6.38 18.7L4 21"></path>
                            <path d="M17.64 18.67L20 21"></path>
                        </svg>
                    </div>
                </div>
                <div class="summary-card-value">{{ $perluPerhatianCount }} kuitansi</div>
                <div class="summary-card-subtext amber">{{ $terlambatCount }} terlambat • {{ $besokCount }} besok</div>
            </div>

            <!-- Card 4: Lunas Bulan Ini -->
            <div class="summary-card">
                <div class="summary-card-header">
                    <span class="summary-card-title">Lunas bulan ini</span>
                    <div class="summary-icon-box rose">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="summary-card-value">Rp{{ number_format($lunasBulanIni, 0, ',', '.') }}</div>
                <div class="summary-card-subtext red">{{ $lunasBulanIniCount }} pembayaran selesai</div>
            </div>
        </div>

        <!-- Middle Row: Arus Utang & Piutang vs Status Pembayaran -->
        <div class="content-grid-row">
            <!-- Left: Arus Utang & Piutang Chart -->
            <div class="card-box">
                <div class="card-box-header">
                    <h2 class="card-box-title">Arus utang & piutang</h2>
                    <span style="font-size: 12px; font-weight: 600; color: #91283b;">{{ $tanggalAcuan->translatedFormat('F Y') }}</span>
                </div>

                <div class="chart-legend-row">
                    <div class="legend-item">
                        <span class="legend-color-box gray"></span>
                        <span>Piutang masuk</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color-box red"></span>
                        <span>Utang keluar</span>
                    </div>
                </div>

                <!-- Dual Bar Chart -->
                <div class="bar-chart-container">
                    @foreach ($chartData as $c)
                        <div class="chart-month-col">
                            <div class="chart-bars-wrap">
                                <div class="bar-item gray" style="height: {{ $c['piutang'] }}%;" title="Piutang {{ $c['bulan'] }}"></div>
                                <div class="bar-item red" style="height: {{ $c['utang'] }}%;" title="Utang {{ $c['bulan'] }}"></div>
                            </div>
                            <span class="month-label">{{ $c['bulan'] }}</span>
                        </div>
                    @endforeach

                    <!-- Floating Detail Pengingat Popup -->
                    <div class="chart-overlay-popup" id="detailPengingatPopup">
                        <div class="popup-header">
                            <div>
                                <div class="popup-title">Detail pengingat</div>
                                <div class="popup-subtitle">{{ $terlambatCount }} terlambat • {{ $besokCount }} jatuh tempo besok</div>
                            </div>
                            <button type="button" class="popup-close-btn" onclick="document.getElementById('detailPengingatPopup').style.display='none'" aria-label="Tutup detail">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>

                        @if ($popupItem)
                            <div class="popup-item-box">
                                <div class="popup-item-left">
                                    <div class="popup-item-icon">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="popup-item-title">{{ $popupItem->tempo_label }} - {{ \Carbon\Carbon::parse($popupItem->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}</div>
                                        <div class="popup-item-meta">{{ $popupItem->nama_pihak }} • {{ $popupItem->nomor_kuitansi }}</div>
                                    </div>
                                </div>
                                <div class="popup-item-amount">
                                    Rp{{ number_format($popupItem->sisa, 0, ',', '.') }}
                                </div>
                            </div>

                            <a href="{{ route('jatuh-tempo.index', ['selected' => $popupItem->id]) }}" class="btn-popup-action">
                                Lihat detail
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Status Pembayaran Breakdown -->
            <div class="card-box">
                <div class="card-box-header">
                    <h2 class="card-box-title">Status pembayaran</h2>
                    <a href="{{ route('rekapitulasi.index') }}" class="card-box-link">Lihat rincian</a>
                </div>

                <div class="status-progress-list">
                    @foreach ($statusPembayaran as $key => $st)
                        <div class="status-progress-item">
                            <div class="status-row-label">
                                <span class="status-name">{{ $st['label'] }}</span>
                                <span class="status-count-pct">{{ $st['count'] }} kuitansi • {{ $st['pct'] }}%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: {{ $st['pct'] }}%; background: {{ $st['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="status-footer-row">
                    <span class="status-footer-label">Total nilai tercatat</span>
                    <span class="status-footer-value">Rp{{ number_format($totalNilaiTercatat, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Perlu Perhatian vs Aktivitas Terbaru -->
        <div class="content-grid-row">
            <!-- Left: Perlu Perhatian List -->
            <div class="card-box">
                <div class="card-box-header">
                    <h2 class="card-box-title">Perlu perhatian</h2>
                    <a href="{{ route('jatuh-tempo.index') }}" class="card-box-link">Lihat semua</a>
                </div>

                <div class="perhatian-list">
                    @forelse ($itemsPerluPerhatian as $item)
                        <a href="{{ route('jatuh-tempo.index', ['selected' => $item->id]) }}" class="perhatian-item">
                            <div class="perhatian-left">
                                <div class="perhatian-icon-box {{ $item->tempo_code === 'terlambat' ? 'red' : 'amber' }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <div class="perhatian-party">{{ $item->nama_pihak }}</div>
                                    <div class="perhatian-meta">{{ $item->nomor_kuitansi }} • Tempo {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}</div>
                                </div>
                            </div>
                            <div class="perhatian-right">
                                <div class="perhatian-nominal">Rp{{ number_format($item->sisa, 0, ',', '.') }}</div>
                                <div class="perhatian-tempo {{ $item->tempo_code === 'terlambat' ? 'red' : 'amber' }}">{{ $item->tempo_label }}</div>
                            </div>
                        </a>
                    @empty
                        <div style="padding: 24px 16px; text-align: center; color: #6b7280; font-size: 13px;">
                            Tidak ada tagihan yang memerlukan perhatian saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Aktivitas Terbaru -->
            <div class="card-box">
                <div class="card-box-header">
                    <h2 class="card-box-title">Aktivitas terbaru</h2>
                </div>

                <div class="aktivitas-list">
                    <!-- Aktivitas 1 -->
                    <div class="aktivitas-item">
                        <div class="aktivitas-icon-box" style="background: #f3f4f6; color: #4b5563;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 6v12M15 9.5H10.5a2 2 0 0 0 0 4h3a2 2 0 0 1 0 4H9"></path>
                            </svg>
                        </div>
                        <div class="aktivitas-content">
                            <div class="aktivitas-title">Pembayaran cicilan dicatat</div>
                            <div class="aktivitas-meta">Warung Bu Sari • Rp2.000.000</div>
                            <div class="aktivitas-time">Hari ini, 09.18</div>
                        </div>
                    </div>

                    <!-- Aktivitas 2 -->
                    <div class="aktivitas-item">
                        <div class="aktivitas-icon-box" style="background: #fef2f2; color: #ef4444;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="12" y1="18" x2="12" y2="12"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                        </div>
                        <div class="aktivitas-content">
                            <div class="aktivitas-title">Kuitansi baru ditambahkan</div>
                            <div class="aktivitas-meta">PT Sumber Pangan • Rp12.400.000</div>
                            <div class="aktivitas-time">Kemarin, 16.42</div>
                        </div>
                    </div>

                    <!-- Aktivitas 3 -->
                    <div class="aktivitas-item">
                        <div class="aktivitas-icon-box" style="background: #eff6ff; color: #3b82f6;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="aktivitas-content">
                            <div class="aktivitas-title">Kuitansi ditandai lunas</div>
                            <div class="aktivitas-meta">CV Segar Abadi • Rp5.800.000</div>
                            <div class="aktivitas-time">20 Sep, 11.05</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Kuitansi Quick View -->
    <div class="modal-backdrop" id="tambahKuitansiModal" onclick="if(event.target === this) closeTambahModal()">
        <div class="modal-card">
            <div class="modal-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: #fdf2f4; color: #91283b; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="12" y1="18" x2="12" y2="12"></line>
                            <line x1="9" y1="15" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 class="modal-title" style="margin: 0;">Tambah Kuitansi Baru</h3>
                        <p style="margin: 2px 0 0; font-size: 12px; color: #696b78;">Pencatatan kuitansi utang atau piutang toko</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeTambahModal()" aria-label="Tutup dialog">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div style="display: grid; gap: 12px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nomor Kuitansi</label>
                        <input type="text" value="KWT-240929" readonly style="width: 100%; box-sizing: border-box; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background: #f9fafb; color: #4b5563;">
                    </div>
                    <div style="position: relative;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Jenis Transaksi</label>
                        <input type="hidden" name="modal_jenis" id="modal_jenis" value="piutang">
                        <div class="modal-custom-select" id="modalJenisSelect" onclick="toggleModalDropdown(this, event)">
                            <span id="modalJenisLabel">Piutang Pelanggan</span>
                            <svg class="dropdown-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                            <div class="custom-dropdown-menu" onclick="event.stopPropagation()">
                                <div class="custom-dropdown-item is-selected" onclick="chooseModalOption('piutang', 'Piutang Pelanggan', event)">
                                    <span>Piutang Pelanggan</span>
                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                <div class="custom-dropdown-item" onclick="chooseModalOption('utang', 'Utang Supplier', event)">
                                    <span>Utang Supplier</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Pihak / Toko</label>
                    <input type="text" placeholder="Contoh: Toko Berkah Jaya" style="width: 100%; box-sizing: border-box; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nominal (Rp)</label>
                        <input type="number" placeholder="0" style="width: 100%; box-sizing: border-box; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Tanggal Jatuh Tempo</label>
                        <input type="date" value="{{ date('Y-m-d', strtotime('+7 days')) }}" style="width: 100%; box-sizing: border-box; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Catatan Transaksi</label>
                    <input type="text" placeholder="Keterangan singkat nota..." style="width: 100%; box-sizing: border-box; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: space-between; align-items: center; border-top: 1px solid #f1f2f5; padding-top: 16px;">
                <a href="{{ route('jatuh-tempo.index') }}" style="font-size: 12px; color: #91283b; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Lihat daftar jatuh tempo
                </a>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn-lihat-detail" onclick="closeTambahModal()">Batal</button>
                    <a href="{{ route('jatuh-tempo.index') }}" class="btn-tambah-kuitansi" style="text-decoration: none;">Kelola di Jatuh Tempo</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTambahModal() {
            document.getElementById('tambahKuitansiModal').style.display = 'flex';
        }
        document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);

    if (params.get('tambah') === '1') {
        openTambahModal();
    }
});
        function closeTambahModal() {
            document.getElementById('tambahKuitansiModal').style.display = 'none';
            const selectEl = document.getElementById('modalJenisSelect');
            if (selectEl) selectEl.classList.remove('is-open');
        }

        function toggleModalDropdown(el, e) {
            if (e) e.stopPropagation();
            el.classList.toggle('is-open');
        }

        function chooseModalOption(val, label, e) {
            if (e) e.stopPropagation();
            document.getElementById('modal_jenis').value = val;
            document.getElementById('modalJenisLabel').textContent = label;
            const container = document.getElementById('modalJenisSelect');
            container.classList.remove('is-open');
            container.querySelectorAll('.custom-dropdown-item').forEach(item => {
                const isSelected = item.textContent.trim().startsWith(label);
                item.classList.toggle('is-selected', isSelected);
                let check = item.querySelector('.dropdown-check');
                if (isSelected && !check) {
                    item.insertAdjacentHTML('beforeend', '<svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>');
                } else if (!isSelected && check) {
                    check.remove();
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.modal-custom-select')) {
                document.querySelectorAll('.modal-custom-select.is-open').forEach(el => el.classList.remove('is-open'));
            }
        });

        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'tambah' || window.location.hash === '#tambah') {
                openTambahModal();
            }
        });
    </script>
</body>
</html>
