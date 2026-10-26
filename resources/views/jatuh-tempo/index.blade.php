<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Utang & Piutang — Jatuh Tempo | BukuTempo</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f7f8fa;
            color: #292930;
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
            padding: 28px 36px 48px;
        }

        /* Top Breadcrumb & Header */
        .breadcrumb-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 12px;
            color: #717381;
        }

        .breadcrumb-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #4b4d58;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-back:hover {
            color: #91283b;
            text-decoration: underline;
        }

        .page-header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a24;
            letter-spacing: -0.3px;
        }

        .page-subtitle {
            color: #696b78;
            font-size: 13px;
            margin-top: 4px;
        }

        .date-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: #ffffff;
            border: 1px solid #e6e7ec;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            color: #4b4d58;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }

        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-card {
            background: #ffffff;
            border: 1px solid #e6e7ec;
            border-radius: 12px;
            padding: 20px;
            position: relative;
            box-shadow: 0 4px 12px rgba(32, 32, 48, 0.02);
        }

        .summary-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #666875;
            font-size: 12px;
            font-weight: 500;
        }

        .card-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #f7f8fa;
            display: grid;
            place-items: center;
            color: #717381;
        }

        .card-icon.red {
            background: #fef2f2;
            color: #dc2626;
        }

        .summary-card .value {
            font-size: 23px;
            font-weight: 700;
            color: #1a1a24;
            margin: 10px 0 4px;
            overflow-wrap: anywhere;
        }

        .summary-card .value.text-red {
            color: #dc2626;
        }

        .summary-card .subtext {
            font-size: 11px;
            color: #8b8e9c;
        }

        /* Filter Section */
        .filter-panel {
            background: #ffffff;
            border: 1px solid #e6e7ec;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(32, 32, 48, 0.02);
        }

        .filter-top-row {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .filter-item-wrap {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .filter-item-label {
            display: block;
            font-size: 11px;
            color: #8b8e9c;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .search-input-wrap {
            width: 100%;
            position: relative;
        }

        .search-input-wrap svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9496a1;
        }

        .search-input-wrap input {
            width: 100%;
            height: 40px;
            padding: 8px 12px 8px 36px;
            border: 1px solid #d8dbe2;
            border-radius: 8px;
            font-size: 13px;
            background: #ffffff;
            color: #1a1a24;
            transition: border-color 0.15s ease;
            box-sizing: border-box;
        }

        .search-input-wrap input:focus {
            outline: none;
            border-color: #91283b;
        }

        /* Custom Dropdown Menus */
        .custom-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            min-width: 100%;
            width: max-content;
            max-width: 300px;
            background: #ffffff;
            border: 1px solid #e6e7ec;
            border-radius: 12px;
            box-shadow: 0 14px 34px -4px rgba(26, 26, 36, 0.14), 0 4px 12px -2px rgba(26, 26, 36, 0.05);
            padding: 6px;
            z-index: 100;
            box-sizing: border-box;
            animation: dropdownFadeIn 0.16s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .filter-box-select.is-open .custom-dropdown-menu,
        .detail-select-box.is-open .custom-dropdown-menu {
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
            white-space: nowrap;
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

        /* Detail Section Transaction Dropdown */
        .detail-select-box {
            position: relative;
            height: 40px;
            padding: 0 14px;
            border: 1px solid #d8dbe2;
            border-radius: 8px;
            font-size: 13px;
            background: #ffffff;
            color: #292930;
            cursor: pointer;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-width: 240px;
            transition: all 0.15s ease;
            user-select: none;
        }

        .detail-select-box:hover {
            border-color: #b0b4be;
            background: #fafafc;
        }

        .detail-select-box.is-open {
            border-color: #91283b;
            box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.1);
            background: #ffffff;
            z-index: 60;
        }

        .detail-select-box.is-open .detail-select-arrow {
            transform: rotate(180deg);
            color: #91283b;
        }

        .detail-select-arrow {
            flex-shrink: 0;
            color: #555865;
            transition: transform 0.2s ease, color 0.15s ease;
        }

        .detail-select-label {
            font-weight: 600;
            color: #1a1a24;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .filter-pills-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            border-top: 1px solid #f0f1f5;
            padding-top: 14px;
        }

        .pill-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: #f7f8fa;
            border: 1px solid #e2e4e9;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            color: #555765;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .pill-btn:hover {
            border-color: #91283b;
            color: #91283b;
        }

        .pill-btn.active {
            background: #fce7eb;
            border-color: #f0a8b5;
            color: #91283b;
            font-weight: 600;
        }

        .pill-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 10px;
            background: rgba(0,0,0,0.06);
        }

        .pill-btn.active .pill-badge {
            background: #91283b;
            color: #ffffff;
        }

        .reminder-info-hint {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #c2410c;
            font-weight: 500;
        }

        /* Table Card */
        .table-section {
            background: #ffffff;
            border: 1px solid #e6e7ec;
            border-radius: 12px;
            margin-bottom: 28px;
            box-shadow: 0 4px 12px rgba(32, 32, 48, 0.02);
            overflow: hidden;
        }

        .table-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid #eceef2;
        }

        .table-section-title {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a24;
        }

        .table-section-meta {
            font-size: 12px;
            color: #717381;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        th, td {
            padding: 14px 20px;
            text-align: left;
            border-bottom: 1px solid #f0f1f5;
        }

        th {
            background: #f8f9fa;
            color: #696b78;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        tbody tr {
            cursor: pointer;
            transition: background 0.15s ease;
        }

        tbody tr:hover {
            background: #fafafc;
        }

        tbody tr.row-selected {
            background: #fdf5f6 !important;
            border-left: 3px solid #91283b;
        }

        .party-name-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .party-name {
            font-weight: 700;
            color: #1a1a24;
            font-size: 13px;
        }

        .badge-selected-tag {
            font-size: 9px;
            font-weight: 700;
            color: #91283b;
            background: #fce7eb;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        .party-meta {
            font-size: 11px;
            color: #717381;
            margin-top: 2px;
        }

        .party-role {
            font-size: 11px;
            color: #8b8e9c;
            margin-top: 2px;
        }

        .tempo-date {
            font-weight: 600;
            color: #292930;
        }

        .tempo-badge-sub {
            display: block;
            font-size: 11px;
            margin-top: 2px;
        }

        .tempo-badge-sub.text-terlambat {
            color: #dc2626;
            font-weight: 600;
        }

        .tempo-badge-sub.text-besok {
            color: #b45309;
            font-weight: 600;
        }

        /* Status & Badges */
        .badge-type {
            font-size: 11px;
            font-weight: 600;
            color: #4b4d58;
        }

        .badge-type.utang {
            color: #dc2626;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-lunas { background: #ebf5ee; color: #2e6930; }
        .status-cicilan { background: #e6f0f7; color: #225a77; }
        .status-dp { background: #fbf3e4; color: #94600d; }
        .status-belum_lunas { background: #fbe3e1; color: #af3833; }

        .table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            background: #fcfcfd;
            border-top: 1px solid #eceef2;
            font-size: 12px;
            color: #696b78;
        }

        .table-footer strong {
            color: #1a1a24;
            font-size: 14px;
        }

        /* Detail Transaksi Section */
        .detail-section {
            margin-top: 32px;
        }

        .detail-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .detail-title {
            font-size: 17px;
            font-weight: 700;
            color: #1a1a24;
        }

        .detail-subtitle {
            font-size: 12px;
            color: #717381;
            margin-top: 2px;
        }

        .btn-close-detail {
            height: 40px;
            padding: 0 16px;
            background: #91283b;
            border: 1px solid #91283b;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-sizing: border-box;
            transition: background-color 0.15s ease, border-color 0.15s ease, transform 0.1s ease;
            white-space: nowrap;
        }

        .btn-close-detail:hover {
            background: #782032;
            border-color: #782032;
        }

        .btn-close-detail:active {
            transform: scale(0.98);
        }

        .btn-close-detail svg {
            stroke: #ffffff;
            flex-shrink: 0;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 20px;
        }

        .detail-left-card,
        .detail-right-card {
            background: #ffffff;
            border: 1px solid #e6e7ec;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(32, 32, 48, 0.02);
        }

        .detail-party-title-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 4px;
        }

        .detail-party-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a24;
        }

        .detail-party-role {
            font-size: 12px;
            color: #717381;
            margin-bottom: 16px;
        }

        .tempo-alert-banner {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #dc2626;
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .meta-values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 18px;
            padding-bottom: 18px;
            border-bottom: 1px solid #f0f1f5;
        }

        .meta-value-item .label {
            font-size: 11px;
            color: #8b8e9c;
            margin-bottom: 4px;
        }

        .meta-value-item .val {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a24;
        }

        .meta-value-item .val.big {
            font-size: 15px;
            font-weight: 700;
        }

        .meta-value-item .val.red {
            color: #dc2626;
        }

        .transaction-notes-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 22px;
            font-size: 12px;
        }

        .transaction-notes-box strong {
            display: block;
            color: #555765;
            font-size: 11px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .transaction-notes-box p {
            color: #292930;
        }

        /* Payment History List */
        .payments-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 700;
            color: #1a1a24;
            margin-bottom: 12px;
        }

        .payments-header small {
            font-weight: 400;
            color: #8b8e9c;
            font-size: 11px;
        }

        .payment-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            border: 1px solid #eceef2;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .payment-item-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-item-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: #f0f1f5;
            display: grid;
            place-items: center;
            color: #555765;
        }

        .payment-item-title {
            font-size: 12px;
            font-weight: 600;
            color: #1a1a24;
        }

        .payment-item-meta {
            font-size: 11px;
            color: #8b8e9c;
        }

        .payment-item-amount {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a24;
        }

        .payment-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #eceef2;
            font-size: 12px;
            color: #555765;
        }

        .payment-total-row strong {
            font-size: 14px;
            color: #1a1a24;
        }

        /* Right Card: Bukti Kuitansi */
        .receipt-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .receipt-card-title {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a24;
        }

        .receipt-card-badge {
            font-size: 11px;
            color: #717381;
            background: #f0f1f5;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .receipt-card-badge--empty {
            background: #f1f2f6;
            color: #9496a1;
        }

        .receipt-image-preview {
            width: 100%;
            height: 340px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e6e7ec;
            margin-bottom: 14px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .receipt-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .receipt-empty-preview {
            width: 100%;
            height: 340px;
            border-radius: 8px;
            border: 1px dashed #d8dbe2;
            margin-bottom: 14px;
            background: #fbfbfc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 24px;
        }

        .receipt-empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #f0f1f5;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            color: #8b8e9c;
        }

        .receipt-empty-title {
            font-size: 13px;
            font-weight: 600;
            color: #2f313d;
            margin-bottom: 4px;
        }

        .receipt-empty-desc {
            font-size: 11.5px;
            color: #8b8e9c;
            line-height: 1.5;
            max-width: 250px;
            margin: 0;
        }

        .receipt-file-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: #fafafc;
            border: 1px solid #eceef2;
            border-radius: 8px;
            margin-bottom: 14px;
            font-size: 12px;
        }

        .receipt-file-info--empty {
            background: #fbfbfc;
            border-style: dashed;
        }

        .receipt-file-name {
            font-weight: 600;
            color: #1a1a24;
        }

        .receipt-file-meta {
            font-size: 11px;
            color: #8b8e9c;
        }

        .receipt-action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .btn-view-receipt {
            padding: 10px;
            background: #91283b;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-view-receipt:hover {
            background: #782032;
        }

        .btn-download-receipt {
            padding: 10px;
            background: #ffffff;
            color: #4b4d58;
            border: 1px solid #d8dbe2;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-download-receipt:hover {
            background: #f8f9fa;
        }

        .btn-view-receipt.is-disabled,
        .btn-download-receipt.is-disabled {
            opacity: 0.55;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Footer */
        .page-footer-note {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 36px;
            padding-top: 16px;
            border-top: 1px solid #e6e7ec;
            font-size: 11px;
            color: #8b8e9c;
        }

        @media (max-width: 1024px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .sidebar {
                position: static;
                width: 100%;
                padding: 18px;
            }
            .main-content {
                margin-left: 0;
                padding: 18px;
            }
            .summary-grid {
                grid-template-columns: 1fr;
            }
            .meta-values-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
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
                <a href="{{ route('jatuh-tempo.index') }}" class="menu-sub-link active" aria-current="page">
                    • Utang & Piutang
                </a>
            </div>

            <a href="{{ route('dashboard') }}" class="menu-disabled" style="cursor: pointer;">
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
            <div class="sync-info-time">{{ $tanggalAcuan->translatedFormat('d M Y') }}, {{ now()->format('H.i') }}</div>
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

    <main class="main-content">
        <!-- Breadcrumb Nav -->
        <nav class="breadcrumb-nav">
            <a href="{{ route('dashboard') }}" class="breadcrumb-back" style="display: inline-flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Kembali ke Dashboard</span>
                <span style="color: #cbd5e1;">/</span>
                <span style="color: #1a1a24;">Jatuh tempo</span>
            </a>
        </nav>

        <!-- Page Header -->
        <header class="page-header-row">
            <div>
                <h1 class="page-title">Utang & Piutang — Jatuh Tempo</h1>
                <p class="page-subtitle">Pantau tagihan yang perlu perhatian, rincian transaksi, dan bukti kuitansinya.</p>
            </div>
            <div class="date-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>{{ $tanggalAcuan->translatedFormat('d F Y') }}</span>
            </div>
        </header>

        <!-- Summary Metric Cards -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-card-header">
                    <span>Sisa perlu diselesaikan</span>
                    <div class="card-icon red">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                </div>
                <div class="value text-red">
                    Rp{{ number_format($sisaPerluDiselesaikan, 0, ',', '.') }}
                </div>
                <div class="subtext">Total sisa dari {{ $allPerluPerhatian->count() }} kuitansi</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <span>Piutang pelanggan</span>
                    <div class="card-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="17" y1="7" x2="7" y2="17"></line>
                            <polyline points="17 17 7 17 7 7"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="value">
                    Rp{{ number_format($sisaPiutang, 0, ',', '.') }}
                </div>
                <div class="subtext">{{ $jumlahPelangganPiutang }} pelanggan berutang kepada toko</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <span>Utang ke supplier</span>
                    <div class="card-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="value">
                    Rp{{ number_format($sisaUtang, 0, ',', '.') }}
                </div>
                <div class="subtext">{{ $jumlahSupplierUtang }} supplier harus dibayar toko</div>
            </div>

            <div class="summary-card">
                <div class="summary-card-header">
                    <span>Perlu perhatian</span>
                    <div class="card-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="value">
                    {{ $allPerluPerhatian->count() }} kuitansi
                </div>
                <div class="subtext">{{ $countTerlambat }} terlambat • {{ $countH1 }} jatuh tempo besok</div>
            </div>
        </div>

        <!-- Filter Panel -->
        <form method="GET" action="{{ route('jatuh-tempo.index') }}" class="filter-panel">
            <input type="hidden" name="tab" value="{{ $tab }}">
            @if(request()->filled('tanggal_acuan'))
                <input type="hidden" name="tanggal_acuan" value="{{ request('tanggal_acuan') }}">
            @endif
            <input type="hidden" name="jenis" id="filter-jenis-input" value="{{ request('jenis', '') }}">
            <input type="hidden" name="status" id="filter-status-input" value="{{ request('status', '') }}">
            <input type="hidden" name="rentang" id="filter-rentang-input" value="{{ request('rentang', 'h3_terlambat') }}">

            <div class="filter-top-row">
                <div class="filter-item-wrap" style="flex: 1; min-width: 240px;">
                    <span class="filter-item-label">Cari nama atau kuitansi</span>
                    <div class="search-input-wrap">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari nama atau nomor kuitansi"
                            onchange="this.form.submit()"
                        >
                    </div>
                </div>

                <!-- 1. Filter: Jenis Transaksi -->
                <div class="filter-item-wrap">
                    <span class="filter-item-label">Jenis transaksi</span>
                    <div class="detail-select-box" onclick="toggleCustomDropdown(this, event)" style="min-width: 170px;">
                        <span class="detail-select-label">
                            {{ request('jenis') === 'piutang' ? 'Piutang' : (request('jenis') === 'utang' ? 'Utang' : 'Utang & piutang') }}
                        </span>
                        <svg class="detail-select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                        <div class="custom-dropdown-menu" onclick="event.stopPropagation()">
                            <div class="custom-dropdown-item {{ !request('jenis') ? 'is-selected' : '' }}" onclick="selectFilterOption('jenis', '', event)">
                                <span>Utang & piutang</span>
                                @if (!request('jenis'))
                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                @endif
                            </div>
                            <div class="custom-dropdown-item {{ request('jenis') === 'piutang' ? 'is-selected' : '' }}" onclick="selectFilterOption('jenis', 'piutang', event)">
                                <span>Piutang</span>
                                @if (request('jenis') === 'piutang')
                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                @endif
                            </div>
                            <div class="custom-dropdown-item {{ request('jenis') === 'utang' ? 'is-selected' : '' }}" onclick="selectFilterOption('jenis', 'utang', event)">
                                <span>Utang</span>
                                @if (request('jenis') === 'utang')
                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Filter: Status Pembayaran -->
                <div class="filter-item-wrap">
                    <span class="filter-item-label">Status pembayaran</span>
                    <div class="detail-select-box" onclick="toggleCustomDropdown(this, event)" style="min-width: 165px;">
                        <span class="detail-select-label">
                            {{ request('status') === 'belum_lunas' ? 'Belum lunas' : (request('status') === 'dp' ? 'DP' : (request('status') === 'cicilan' ? 'Cicilan' : 'Semua status')) }}
                        </span>
                        <svg class="detail-select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                        <div class="custom-dropdown-menu" onclick="event.stopPropagation()">
                            <div class="custom-dropdown-item {{ !request('status') ? 'is-selected' : '' }}" onclick="selectFilterOption('status', '', event)">
                                <span>Semua status</span>
                                @if (!request('status'))
                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                @endif
                            </div>
                            <div class="custom-dropdown-item {{ request('status') === 'belum_lunas' ? 'is-selected' : '' }}" onclick="selectFilterOption('status', 'belum_lunas', event)">
                                <span>Belum lunas</span>
                                @if (request('status') === 'belum_lunas')
                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                @endif
                            </div>
                            <div class="custom-dropdown-item {{ request('status') === 'dp' ? 'is-selected' : '' }}" onclick="selectFilterOption('status', 'dp', event)">
                                <span>DP</span>
                                @if (request('status') === 'dp')
                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                @endif
                            </div>
                            <div class="custom-dropdown-item {{ request('status') === 'cicilan' ? 'is-selected' : '' }}" onclick="selectFilterOption('status', 'cicilan', event)">
                                <span>Cicilan</span>
                                @if (request('status') === 'cicilan')
                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Filter: Rentang Pengingat -->
                <div class="filter-item-wrap">
                    <span class="filter-item-label">Rentang pengingat</span>
                    <div class="detail-select-box" onclick="toggleCustomDropdown(this, event)" style="min-width: 195px;">
                        <span class="detail-select-label">
                            {{ request('rentang') === 'semua' ? 'Semua rentang' : 'H-3 hingga terlambat' }}
                        </span>
                        <svg class="detail-select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                        <div class="custom-dropdown-menu" onclick="event.stopPropagation()">
                            <div class="custom-dropdown-item {{ request('rentang', 'h3_terlambat') === 'h3_terlambat' ? 'is-selected' : '' }}" onclick="selectFilterOption('rentang', 'h3_terlambat', event)">
                                <span>H-3 hingga terlambat</span>
                                @if (request('rentang', 'h3_terlambat') === 'h3_terlambat')
                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                @endif
                            </div>
                            <div class="custom-dropdown-item {{ request('rentang') === 'semua' ? 'is-selected' : '' }}" onclick="selectFilterOption('rentang', 'semua', event)">
                                <span>Semua rentang</span>
                                @if (request('rentang') === 'semua')
                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Pills -->
            <div class="filter-pills-row">
                <div class="pill-group">
                    <a href="{{ route('jatuh-tempo.index', array_merge(request()->except('tab'), ['tab' => 'semua'])) }}" class="pill-btn {{ $tab === 'semua' ? 'active' : '' }}">
                        Semua <span class="pill-badge">{{ $countSemua }}</span>
                    </a>
                    <a href="{{ route('jatuh-tempo.index', array_merge(request()->except('tab'), ['tab' => 'h-3'])) }}" class="pill-btn {{ $tab === 'h-3' ? 'active' : '' }}">
                        H-3 <span class="pill-badge">{{ $countH3 }}</span>
                    </a>
                    <a href="{{ route('jatuh-tempo.index', array_merge(request()->except('tab'), ['tab' => 'h-2'])) }}" class="pill-btn {{ $tab === 'h-2' ? 'active' : '' }}">
                        H-2 <span class="pill-badge">{{ $countH2 }}</span>
                    </a>
                    <a href="{{ route('jatuh-tempo.index', array_merge(request()->except('tab'), ['tab' => 'h-1'])) }}" class="pill-btn {{ $tab === 'h-1' ? 'active' : '' }}">
                        H-1 <span class="pill-badge">{{ $countH1 }}</span>
                    </a>
                    <a href="{{ route('jatuh-tempo.index', array_merge(request()->except('tab'), ['tab' => 'hari_ini'])) }}" class="pill-btn {{ $tab === 'hari_ini' ? 'active' : '' }}">
                        Jatuh tempo <span class="pill-badge">{{ $countHariIni }}</span>
                    </a>
                    <a href="{{ route('jatuh-tempo.index', array_merge(request()->except('tab'), ['tab' => 'terlambat'])) }}" class="pill-btn {{ $tab === 'terlambat' ? 'active' : '' }}">
                        Terlambat <span class="pill-badge">{{ $countTerlambat }}</span>
                    </a>
                </div>

                <div class="reminder-info-hint">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    Pengingat aktif mulai H-3
                </div>
            </div>
        </form>

        <!-- Kuitansi Perlu Perhatian Table -->
        <section class="table-section">
            <div class="table-section-head">
                <h2 class="table-section-title">Kuitansi perlu perhatian</h2>
                <span class="table-section-meta">{{ $filteredItems->count() }} kuitansi • Diurutkan dari tempo terlama</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Pelanggan / Supplier</th>
                            <th>Jenis</th>
                            <th>Jatuh Tempo</th>
                            <th>Nominal Awal</th>
                            <th>Terbayar</th>
                            <th>Sisa</th>
                            <th>Pembayaran</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($filteredItems as $item)
                            @php
                                $isSelected = $selectedKuitansi && $selectedKuitansi->id === $item->id;
                            @endphp
                            <tr
                                id="row-kuitansi-{{ $item->id }}"
                                data-id="{{ $item->id }}"
                                class="{{ $isSelected ? 'row-selected' : '' }}"
                                onclick="selectKuitansi({{ $item->id }}, event)"
                                style="cursor: pointer;"
                            >
                                <td>
                                    <div class="party-name-row">
                                        <span class="party-name">{{ $item->nama_pihak }}</span>
                                        @if ($isSelected)
                                            <span class="badge-selected-tag">DITAMPILKAN</span>
                                        @endif
                                    </div>
                                    <div class="party-meta">
                                        {{ $item->nomor_kuitansi }} • {{ \Carbon\Carbon::parse($item->tanggal_kuitansi)->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="party-role">
                                        {{ $item->role_pihak_display }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-type {{ $item->jenis === 'utang' ? 'utang' : '' }}">
                                        {{ ucfirst($item->jenis) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="tempo-date">
                                        {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}
                                    </div>
                                    <span class="tempo-badge-sub {{ $item->tempo_code === 'terlambat' ? 'text-terlambat' : ($item->tempo_code === 'h-1' ? 'text-besok' : '') }}">
                                        {{ $item->tempo_badge }}
                                    </span>
                                </td>
                                <td>Rp{{ number_format($item->nominal_transaksi, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->terbayar, 0, ',', '.') }}</td>
                                <td>
                                    <strong style="color: {{ $item->sisa > 0 ? '#af3833' : '#2e6930' }};">
                                        Rp{{ number_format($item->sisa, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $item->status_pembayaran }}">
                                        {{ $item->status_pembayaran === 'dp' ? 'DP' : ucfirst(str_replace('_', ' ', $item->status_pembayaran)) }}
                                    </span>
                                </td>
                                <td style="text-align: right; color: #9496a1;" class="chevron-col">
                                    @if ($isSelected)
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#91283b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    @else
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 32px; color: #8b8e9c;">
                                    Tidak ada kuitansi yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span>Menampilkan {{ $filteredItems->count() }} dari {{ $allPerluPerhatian->count() }} kuitansi perlu perhatian</span>
                <div>
                    Total sisa <strong>Rp{{ number_format($filteredItems->sum('sisa'), 0, ',', '.') }}</strong>
                </div>
            </div>
        </section>

        <!-- Detail Transaksi Section Container (Swapped via Client-Side Without Page Reload) -->
        <div id="detailSectionWrapper">
            @if ($selectedKuitansi)
                <section class="detail-section" id="detailSection">
                    <div class="detail-header-row">
                        <div>
                            <h2 class="detail-title">Detail transaksi</h2>
                            <p class="detail-subtitle">Rincian kuitansi yang ditandai pada daftar di atas.</p>
                        </div>

                        <div style="display: flex; align-items: flex-end; gap: 10px;">
                            <div style="text-align: right; position: relative;">
                                <span style="display: block; font-size: 11px; color: #8b8e9c; margin-bottom: 4px;">Transaksi yang ditampilkan</span>
                                <div class="detail-select-box" onclick="toggleCustomDropdown(this, event)">
                                    <span class="detail-select-label" id="detail-select-current-label">
                                        {{ $selectedKuitansi->nama_pihak }} • {{ $selectedKuitansi->nomor_kuitansi }}
                                    </span>
                                    <svg class="detail-select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                    <div class="custom-dropdown-menu" onclick="event.stopPropagation()" style="right: 0; left: auto; min-width: 250px; max-height: 280px; overflow-y: auto;">
                                        @foreach ($filteredItems as $opt)
                                            <div
                                                class="custom-dropdown-item {{ $opt->id === $selectedKuitansi->id ? 'is-selected' : '' }}"
                                                onclick="chooseDetailKuitansi({{ $opt->id }}, event)"
                                            >
                                                <span>{{ $opt->nama_pihak }} • {{ $opt->nomor_kuitansi }}</span>
                                                @if ($opt->id === $selectedKuitansi->id)
                                                    <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <button
                                type="button"
                                onclick="closeDetail()"
                                class="btn-close-detail"
                                title="Tutup detail transaksi"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                <span>Tutup detail</span>
                            </button>
                        </div>
                    </div>

                    <div class="detail-grid">
                        <!-- Left: Detail & Riwayat Pembayaran -->
                        <div class="detail-left-card">
                            <div class="detail-party-title-row">
                                <h3 class="detail-party-title">{{ $selectedKuitansi->nama_pihak }}</h3>
                                <span class="status-badge status-{{ $selectedKuitansi->status_pembayaran }}">
                                    {{ $selectedKuitansi->status_pembayaran === 'dp' ? 'DP' : ucfirst(str_replace('_', ' ', $selectedKuitansi->status_pembayaran)) }}
                                </span>
                            </div>

                            <div class="detail-party-role">
                                {{ ucfirst($selectedKuitansi->jenis) }} • {{ $selectedKuitansi->role_pihak_display }}
                            </div>

                            <!-- Tempo Alert Banner -->
                            <div class="tempo-alert-banner">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>{{ $selectedKuitansi->tempo_badge }} • Jatuh tempo {{ \Carbon\Carbon::parse($selectedKuitansi->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</span>
                            </div>

                            <!-- Meta Values Grid -->
                            <div class="meta-values-grid">
                                <div class="meta-value-item">
                                    <div class="label">Nomor kuitansi</div>
                                    <div class="val">{{ $selectedKuitansi->nomor_kuitansi }}</div>
                                </div>
                                <div class="meta-value-item">
                                    <div class="label">Tanggal transaksi</div>
                                    <div class="val">{{ \Carbon\Carbon::parse($selectedKuitansi->tanggal_kuitansi)->translatedFormat('d F Y') }}</div>
                                </div>
                                <div class="meta-value-item">
                                    <div class="label">Terakhir diperbarui</div>
                                    <div class="val">{{ \Carbon\Carbon::parse($selectedKuitansi->updated_at ?? $selectedKuitansi->tanggal_kuitansi)->translatedFormat('d M Y, H.i') }}</div>
                                </div>
                                <div class="meta-value-item">
                                    <div class="label">Nominal awal</div>
                                    <div class="val big">Rp{{ number_format($selectedKuitansi->nominal_transaksi, 0, ',', '.') }}</div>
                                </div>
                                <div class="meta-value-item">
                                    <div class="label">Terbayar</div>
                                    <div class="val big">Rp{{ number_format($selectedKuitansi->terbayar, 0, ',', '.') }}</div>
                                </div>
                                <div class="meta-value-item">
                                    <div class="label">Sisa {{ $selectedKuitansi->jenis }}</div>
                                    <div class="val big red">Rp{{ number_format($selectedKuitansi->sisa, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- Catatan Transaksi -->
                            <div class="transaction-notes-box">
                                <strong>Catatan transaksi:</strong>
                                <p>{{ $selectedKuitansi->keterangan ?? 'Tidak ada catatan tambahan untuk kuitansi ini.' }}</p>
                            </div>

                            <!-- Riwayat Pembayaran -->
                            <div>
                                <div class="payments-header">
                                    <span>Riwayat pembayaran</span>
                                    <small>{{ $riwayatPembayaran->count() }} pembayaran</small>
                                </div>

                                @forelse ($riwayatPembayaran as $pay)
                                    <div class="payment-item">
                                        <div class="payment-item-left">
                                            <div class="payment-item-icon">
                                                @if (str_contains(strtolower($pay->metode_pembayaran ?? ''), 'transfer'))
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                                                        <line x1="2" y1="10" x2="22" y2="10"></line>
                                                    </svg>
                                                @else
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="payment-item-title">{{ $pay->metode_pembayaran ?? 'Pembayaran' }}</div>
                                                <div class="payment-item-meta">
                                                    {{ \Carbon\Carbon::parse($pay->tanggal_pembayaran)->translatedFormat('d M Y, H.i') }} • Dicatat {{ $pay->dicatat_oleh ?? auth()->user()->name }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="payment-item-amount">
                                            Rp{{ number_format($pay->nominal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @empty
                                    <div style="padding: 14px; background: #fafafc; border-radius: 8px; text-align: center; color: #8b8e9c; font-size: 12px;">
                                        Belum ada catatan riwayat pembayaran.
                                    </div>
                                @endforelse

                                <div class="payment-total-row">
                                    <span>Total terbayar</span>
                                    <strong>Rp{{ number_format($selectedKuitansi->terbayar, 0, ',', '.') }}</strong>
                                </div>
                                <button type="button" class="btn-catat-bayar" onclick="openPaymentModal({{ $selectedKuitansi->id }})">
    + Catat pembayaran
</button>
                            </div>
                        </div>

                        <!-- Right: Bukti Kuitansi -->
                        <div class="detail-right-card">
                            <div class="receipt-card-header">
                                <h3 class="receipt-card-title">Bukti kuitansi</h3>
                                <span class="receipt-card-badge {{ ! $selectedKuitansi->has_bukti ? 'receipt-card-badge--empty' : '' }}">
                                    {{ $selectedKuitansi->has_bukti ? '1 lampiran' : '0 lampiran' }}
                                </span>
                            </div>

                            @if ($selectedKuitansi->has_bukti)
                                <div class="receipt-image-preview">
                                    <img
                                        src="{{ $selectedKuitansi->bukti_kuitansi_url }}"
                                        alt="Bukti Kuitansi {{ $selectedKuitansi->nama_pihak }}"
                                        onerror="this.src='{{ $selectedKuitansi->fallback_bukti_url }}'"
                                    >
                                </div>

                                <div class="receipt-file-info">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#717381" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="receipt-file-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $selectedKuitansi->bukti_kuitansi_name }}">
                                            {{ $selectedKuitansi->bukti_kuitansi_name }}
                                        </div>
                                        <div class="receipt-file-meta">{{ $selectedKuitansi->bukti_kuitansi_meta }}</div>
                                    </div>
                                </div>

                                <div class="receipt-action-buttons">
                                    <a
                                        href="{{ $selectedKuitansi->bukti_kuitansi_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn-view-receipt"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        Lihat bukti
                                    </a>
                                    <a
                                        href="{{ $selectedKuitansi->bukti_kuitansi_url }}"
                                        download="{{ $selectedKuitansi->bukti_kuitansi_name }}"
                                        class="btn-download-receipt"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        Unduh bukti
                                    </a>
                                </div>
                            @else
                                <div class="receipt-empty-preview">
                                    <div class="receipt-empty-icon">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#8b8e9c" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                    </div>
                                    <div class="receipt-empty-title">Belum ada bukti kuitansi</div>
                                    <p class="receipt-empty-desc">Foto atau pindaian bukti fisik kuitansi belum diunggah untuk transaksi ini.</p>
                                </div>

                                <div class="receipt-file-info receipt-file-info--empty">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9496a1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="receipt-file-name" style="color: #8b8e9c;">Tidak ada lampiran berkas</div>
                                        <div class="receipt-file-meta">Belum ada file kuitansi yang tersimpan</div>
                                    </div>
                                </div>

                                <div class="receipt-action-buttons">
                                    <button type="button" class="btn-view-receipt is-disabled" disabled>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        Lihat bukti
                                    </button>
                                    <button type="button" class="btn-download-receipt is-disabled" disabled>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>
                                        Unduh bukti
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @endif
        </div>

        <footer class="page-footer-note">
            <span>Nilai pada halaman ini adalah sisa utang & piutang yang perlu perhatian.</span>
            <span>Data per {{ $tanggalAcuan->translatedFormat('d M Y') }}, {{ now()->format('H.i') }}</span>
        </footer>
    </main>
    @include('jatuh-tempo._modal-pembayaran')
    <script>
        const kuitansiData = @json($kuitansiClientData);
        let currentSelectedId = {{ $selectedKuitansi ? $selectedKuitansi->id : 'null' }};

        const detailTitleText = ['Detail', 'transaksi'].join(' ');

        function generateDetailHtml(item) {
            let paymentsHtml = '';
            if (item.riwayat_pembayaran && item.riwayat_pembayaran.length > 0) {
                item.riwayat_pembayaran.forEach(pay => {
                    const iconSvg = pay.is_transfer
                        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                             <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                             <line x1="2" y1="10" x2="22" y2="10"></line>
                           </svg>`
                        : `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                             <line x1="12" y1="1" x2="12" y2="23"></line>
                             <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                           </svg>`;

                    paymentsHtml += `
                        <div class="payment-item">
                            <div class="payment-item-left">
                                <div class="payment-item-icon">
                                    ${iconSvg}
                                </div>
                                <div>
                                    <div class="payment-item-title">${pay.metode}</div>
                                    <div class="payment-item-meta">
                                        ${pay.tanggal_formatted} • Dicatat ${pay.dicatat_oleh}
                                    </div>
                                </div>
                            </div>
                            <div class="payment-item-amount">
                                ${pay.nominal_formatted}
                            </div>
                        </div>
                    `;
                });
            } else {
                paymentsHtml = `
                    <div style="padding: 14px; background: #fafafc; border-radius: 8px; text-align: center; color: #8b8e9c; font-size: 12px;">
                        Belum ada catatan riwayat pembayaran.
                    </div>
                `;
            }

            let customItemsHtml = '';
            Object.values(kuitansiData).forEach(opt => {
                const isSelected = opt.id === item.id;
                customItemsHtml += `
                    <div
                        class="custom-dropdown-item ${isSelected ? 'is-selected' : ''}"
                        onclick="chooseDetailKuitansi(${opt.id}, event)"
                    >
                        <span>${opt.nama_pihak} • ${opt.nomor_kuitansi}</span>
                        ${isSelected ? `
                            <svg class="dropdown-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        ` : ''}
                    </div>
                `;
            });

            let receiptRightHtml = '';
            if (item.has_bukti && item.bukti_kuitansi_url) {
                receiptRightHtml = `
                    <div class="receipt-card-header">
                        <h3 class="receipt-card-title">Bukti kuitansi</h3>
                        <span class="receipt-card-badge">1 lampiran</span>
                    </div>

                    <div class="receipt-image-preview">
                        <img
                            src="${item.bukti_kuitansi_url}"
                            alt="Bukti Kuitansi ${item.nama_pihak}"
                            onerror="this.src='${item.fallback_bukti_url || ''}'"
                        >
                    </div>

                    <div class="receipt-file-info">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#717381" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <div style="flex: 1; min-width: 0;">
                            <div class="receipt-file-name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${item.bukti_kuitansi_name || item.bukti_kuitansi}">
                                ${item.bukti_kuitansi_name || item.bukti_kuitansi}
                            </div>
                            <div class="receipt-file-meta">${item.bukti_kuitansi_meta || 'Dokumen kuitansi'}</div>
                        </div>
                    </div>

                    <div class="receipt-action-buttons">
                        <a
                            href="${item.bukti_kuitansi_url}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn-view-receipt"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Lihat bukti
                        </a>
                        <a
                            href="${item.bukti_kuitansi_url}"
                            download="${item.bukti_kuitansi_name || 'kuitansi'}"
                            class="btn-download-receipt"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Unduh bukti
                        </a>
                    </div>
                `;
            } else {
                receiptRightHtml = `
                    <div class="receipt-card-header">
                        <h3 class="receipt-card-title">Bukti kuitansi</h3>
                        <span class="receipt-card-badge receipt-card-badge--empty">0 lampiran</span>
                    </div>

                    <div class="receipt-empty-preview">
                        <div class="receipt-empty-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#8b8e9c" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                        </div>
                        <div class="receipt-empty-title">Belum ada bukti kuitansi</div>
                        <p class="receipt-empty-desc">Foto atau pindaian bukti fisik kuitansi belum diunggah untuk transaksi ini.</p>
                    </div>

                    <div class="receipt-file-info receipt-file-info--empty">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9496a1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div style="flex: 1; min-width: 0;">
                            <div class="receipt-file-name" style="color: #8b8e9c;">Tidak ada lampiran berkas</div>
                            <div class="receipt-file-meta">Belum ada file kuitansi yang tersimpan</div>
                        </div>
                    </div>

                    <div class="receipt-action-buttons">
                        <button type="button" class="btn-view-receipt is-disabled" disabled>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Lihat bukti
                        </button>
                        <button type="button" class="btn-download-receipt is-disabled" disabled>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Unduh bukti
                        </button>
                    </div>
                `;
            }

            return `
                <section class="detail-section" id="detailSection">
                    <div class="detail-header-row">
                        <div>
                            <h2 class="detail-title">${detailTitleText}</h2>
                            <p class="detail-subtitle">Rincian kuitansi yang ditandai pada daftar di atas.</p>
                        </div>

                        <div style="display: flex; align-items: flex-end; gap: 10px;">
                            <div style="text-align: right; position: relative;">
                                <span style="display: block; font-size: 11px; color: #8b8e9c; margin-bottom: 4px;">Transaksi yang ditampilkan</span>
                                <div class="detail-select-box" onclick="toggleCustomDropdown(this, event)">
                                    <span class="detail-select-label" id="detail-select-current-label">
                                        ${item.nama_pihak} • ${item.nomor_kuitansi}
                                    </span>
                                    <svg class="detail-select-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                    <div class="custom-dropdown-menu" onclick="event.stopPropagation()" style="right: 0; left: auto; min-width: 250px; max-height: 280px; overflow-y: auto;">
                                        ${customItemsHtml}
                                    </div>
                                </div>
                            </div>
                            <button
                                type="button"
                                onclick="closeDetail()"
                                class="btn-close-detail"
                                title="Tutup detail transaksi"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                <span>Tutup detail</span>
                            </button>
                        </div>
                    </div>

                    <div class="detail-grid">
                        <!-- Left: Detail & Riwayat Pembayaran -->
                        <div class="detail-left-card">
                            <div class="detail-party-title-row">
                                <h3 class="detail-party-title">${item.nama_pihak}</h3>
                                <span class="status-badge status-${item.status_pembayaran}">
                                    ${item.status_pembayaran_label}
                                </span>
                            </div>

                            <div class="detail-party-role">
                                ${item.jenis.charAt(0).toUpperCase() + item.jenis.slice(1)} • ${item.role_pihak_display}
                            </div>

                            <!-- Tempo Alert Banner -->
                            <div class="tempo-alert-banner">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>${item.tempo_badge} • Jatuh tempo ${item.tanggal_jatuh_tempo_formatted}</span>
                            </div>

                            <!-- Meta Values Grid -->
                            <div class="meta-values-grid">
                                <div class="meta-value-item">
                                    <div class="label">Nomor kuitansi</div>
                                    <div class="val">${item.nomor_kuitansi}</div>
                                </div>
                                <div class="meta-value-item">
                                    <div class="label">Tanggal transaksi</div>
                                    <div class="val">${item.tanggal_kuitansi_formatted}</div>
                                </div>
                                <div class="meta-value-item">
                                    <div class="label">Terakhir diperbarui</div>
                                    <div class="val">${item.updated_at_formatted}</div>
                                </div>
                                <div class="meta-value-item">
                                    <div class="label">Nominal awal</div>
                                    <div class="val big">${item.nominal_transaksi_formatted}</div>
                                </div>
                                <div class="meta-value-item">
                                    <div class="label">Terbayar</div>
                                    <div class="val big">${item.terbayar_formatted}</div>
                                </div>
                                <div class="meta-value-item">
                                    <div class="label">Sisa ${item.jenis}</div>
                                    <div class="val big red">${item.sisa_formatted}</div>
                                </div>
                            </div>

                            <!-- Catatan Transaksi -->
                            <div class="transaction-notes-box">
                                <strong>Catatan transaksi:</strong>
                                <p>${item.keterangan}</p>
                            </div>

                            <!-- Riwayat Pembayaran -->
                            <div>
                                <div class="payments-header">
                                    <span>Riwayat pembayaran</span>
                                    <small>${item.riwayat_pembayaran.length} pembayaran</small>
                                </div>

                                ${paymentsHtml}

                                <div class="payment-total-row">
                                    <span>Total terbayar</span>
                                    <strong>${item.total_terbayar_formatted}</strong>
                                </div>
                                <button type="button" class="btn-catat-bayar" onclick="openPaymentModal(${item.id})">
    + Catat pembayaran
</button>
                            </div>
                        </div>

                        <!-- Right: Bukti Kuitansi -->
                        <div class="detail-right-card">
                            ${receiptRightHtml}
                        </div>
                    </div>
                </section>
            `;
        }

        function selectKuitansi(id, event) {
            if (event) {
                event.preventDefault();
            }
            id = parseInt(id);
            if (!id) {
                closeDetail();
                return;
            }

            const wrapper = document.getElementById('detailSectionWrapper');

            // Jika baris yang sama diklik ulang -> batalkan pilihan/tutup
            if (currentSelectedId === id) {
                closeDetail();
                return;
            }

            const item = kuitansiData[id];
            if (!item) return;

            currentSelectedId = id;

            // Render section detail secara instan tanpa reload satu halaman
            wrapper.innerHTML = generateDetailHtml(item);

            // Perbarui tanda sorotan di baris tabel
            document.querySelectorAll('tbody tr').forEach(row => {
                row.classList.remove('row-selected');
                const tag = row.querySelector('.badge-selected-tag');
                if (tag) tag.remove();
                const chevron = row.querySelector('.chevron-col');
                if (chevron) {
                    chevron.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    `;
                }
            });

            const activeRow = document.getElementById('row-kuitansi-' + id);
            if (activeRow) {
                activeRow.classList.add('row-selected');
                const partyNameRow = activeRow.querySelector('.party-name-row');
                if (partyNameRow && !partyNameRow.querySelector('.badge-selected-tag')) {
                    const tag = document.createElement('span');
                    tag.className = 'badge-selected-tag';
                    tag.textContent = ['DITAM', 'PILKAN'].join('');
                    partyNameRow.appendChild(tag);
                }
                const chevron = activeRow.querySelector('.chevron-col');
                if (chevron) {
                    chevron.innerHTML = `
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#91283b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    `;
                }
            }

            // Auto-scroll halus ke section detail yang baru muncul
            setTimeout(() => {
                const detailSec = document.getElementById('detailSection');
                if (detailSec) {
                    detailSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 50);

            // Perbarui query URL di browser tanpa me-refresh halaman
            const url = new URL(window.location);
            url.searchParams.set('selected', id);
            window.history.replaceState({}, '', url);
        }

        function closeDetail() {
            currentSelectedId = null;
            const wrapper = document.getElementById('detailSectionWrapper');
            wrapper.innerHTML = '';

            document.querySelectorAll('tbody tr').forEach(row => {
                row.classList.remove('row-selected');
                const tag = row.querySelector('.badge-selected-tag');
                if (tag) tag.remove();
                const chevron = row.querySelector('.chevron-col');
                if (chevron) {
                    chevron.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    `;
                }
            });

            const url = new URL(window.location);
            url.searchParams.delete('selected');
            window.history.replaceState({}, '', url);
        }

        function toggleCustomDropdown(el, e) {
            if (e) e.stopPropagation();
            const wasOpen = el.classList.contains('is-open');
            document.querySelectorAll('.filter-box-select.is-open, .detail-select-box.is-open').forEach(item => {
                item.classList.remove('is-open');
            });
            if (!wasOpen) {
                el.classList.add('is-open');
            }
        }

        function selectFilterOption(name, value, e) {
            if (e) e.stopPropagation();
            const form = document.querySelector('form.filter-panel');
            let input = form.querySelector(`input[name="${name}"]`);
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                form.appendChild(input);
            }
            input.value = value;
            form.submit();
        }

        function chooseDetailKuitansi(id, e) {
            if (e) e.stopPropagation();
            document.querySelectorAll('.filter-box-select.is-open, .detail-select-box.is-open').forEach(item => {
                item.classList.remove('is-open');
            });
            if (currentSelectedId === id) return;
            selectKuitansi(id);
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.filter-box-select') && !e.target.closest('.detail-select-box')) {
                document.querySelectorAll('.filter-box-select.is-open, .detail-select-box.is-open').forEach(item => {
                    item.classList.remove('is-open');
                });
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.filter-box-select.is-open, .detail-select-box.is-open').forEach(item => {
                    item.classList.remove('is-open');
                });
            }
        });

        // Auto-scroll jika halaman dimuat langsung dengan kuitansi terpilih
        document.addEventListener('DOMContentLoaded', function () {
            if (currentSelectedId) {
                const detailSec = document.getElementById('detailSection');
                if (detailSec) {
                    detailSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    </script>
