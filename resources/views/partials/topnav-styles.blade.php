<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
{{-- Font dimuat tanpa memblokir render: jika Google Fonts lambat/tidak terjangkau, halaman tetap tampil dengan font sistem. --}}
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
<noscript>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap">
</noscript>
<style>
    :root {
        --ink: #2a2a5c;
        --ink-strong: #23224f;
        --muted: #6b6b86;
        --accent: #a3153f;
        --stage: #f0efed;
        --board: #f8f8f7;
        --field: #eeede9;
        --card: #f2f1ee;
        --danger-bg: #fdeaed;
        --warn-bg: #fdf1d3;
        --warn-ink: #8a5a00;
    }

    .topnav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 4px 8px 4px 4px;
    }

    .topnav-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: var(--ink-strong);
        flex-shrink: 0;
    }

    .topnav-mark {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: var(--ink);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .topnav-name {
        display: block;
        font-size: 15px;
        font-weight: 800;
        line-height: 1.1;
    }

    .topnav-tag {
        display: block;
        margin-top: 3px;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 1.4px;
        color: var(--muted);
    }

    .topnav-links {
        display: flex;
        gap: 2px;
        padding: 4px;
        border-radius: 999px;
        background: #fff;
        box-shadow: 0 6px 18px -10px rgba(42, 42, 92, 0.25);
        overflow-x: auto;
        scrollbar-width: none;
    }

    .topnav-links::-webkit-scrollbar {
        display: none;
    }

    .topnav-links a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: var(--muted);
        text-decoration: none;
        white-space: nowrap;
    }

    .topnav-links a:hover {
        color: var(--ink);
    }

    .topnav-icon {
        flex-shrink: 0;
    }

    .topnav-links a.is-active {
        background: var(--ink);
        color: #fff;
        font-weight: 700;
    }

    .topnav-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    .bell {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #fff;
        color: var(--ink);
        box-shadow: 0 6px 18px -10px rgba(42, 42, 92, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .bell-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 999px;
        background: #d12f4d;
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--stage);
    }

    .akun {
        position: relative;
    }

    .akun summary {
        list-style: none;
        cursor: pointer;
    }

    .akun summary::-webkit-details-marker {
        display: none;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--ink);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .akun-menu {
        position: absolute;
        right: 0;
        top: 48px;
        z-index: 20;
        width: 220px;
        padding: 14px;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 24px 48px -16px rgba(42, 42, 92, 0.35);
    }

    .akun-menu strong {
        display: block;
        font-size: 13.5px;
        font-weight: 800;
        color: var(--ink);
    }

    .akun-menu span {
        display: block;
        margin: 2px 0 12px;
        font-size: 12px;
        color: var(--muted);
    }

    .akun-menu button {
        width: 100%;
        height: 40px;
        border: 0;
        border-radius: 12px;
        background: var(--ink);
        color: #fff;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .akun-menu button:hover {
        background: var(--ink-strong);
    }

    :focus-visible {
        outline: 3px solid rgba(42, 42, 92, 0.4);
        outline-offset: 2px;
    }

    @media (max-width: 860px) {
        .topnav {
            flex-wrap: wrap;
        }

        .topnav-links {
            order: 3;
            width: 100%;
        }

        .topnav-actions {
            margin-left: auto;
        }
    }
</style>