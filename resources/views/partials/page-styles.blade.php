<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Manrope', system-ui, -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;
        background: #fff;
        color: var(--ink);
        padding: 12px;
        -webkit-font-smoothing: antialiased;
    }

    a { color: inherit; }

    .shell { background: var(--stage); border-radius: 40px; padding: 16px 18px 18px; max-width: 1240px; margin: 0 auto; }
    .board { margin-top: 14px; background: var(--board); border-radius: 32px; padding: 30px 30px 32px; box-shadow: 0 30px 60px -34px rgba(42, 42, 92, 0.3); }

    .page-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .page-head h1 { font-size: 26px; font-weight: 800; letter-spacing: -0.5px; line-height: 1.15; }
    .page-head p { margin-top: 6px; font-size: 13px; font-weight: 500; color: var(--muted); }

    .btn-primary {
        display: inline-flex; align-items: center; gap: 8px;
        height: 44px; padding: 0 22px; border-radius: 999px; border: 0; cursor: pointer;
        background: var(--ink); color: #fff; text-decoration: none; font: inherit;
        font-size: 13.5px; font-weight: 700;
        box-shadow: 0 14px 26px -12px rgba(42, 42, 92, 0.6);
    }
    .btn-primary:hover { background: var(--ink-strong); }

    .btn-soft {
        display: inline-flex; align-items: center; justify-content: center;
        height: 36px; padding: 0 16px; border-radius: 999px; border: 0; cursor: pointer;
        background: #fff; color: var(--ink); text-decoration: none; font: inherit;
        font-size: 12.5px; font-weight: 700; white-space: nowrap;
        box-shadow: 0 6px 14px -8px rgba(42, 42, 92, 0.4);
    }
    .btn-soft:hover { background: #f4f3f0; }

    .alert { margin-top: 18px; padding: 12px 16px; border-radius: 16px; font-size: 13px; font-weight: 600; }
    .alert.ok { background: #e9f6ef; color: #1f6b44; }
    .alert.err { background: var(--danger-bg); color: #8f1b34; }

    .panel-card { margin-top: 18px; background: var(--card); border-radius: 26px; padding: 22px 22px 20px; box-shadow: 0 14px 26px -22px rgba(42, 42, 92, 0.45); }

    .tabel-wrap { overflow-x: auto; }
    table.tabel { width: 100%; border-collapse: collapse; min-width: 760px; }
    .tabel th { padding: 0 10px 12px; text-align: left; font-size: 10.5px; font-weight: 700; letter-spacing: 0.9px; text-transform: uppercase; color: var(--muted); white-space: nowrap; }
    .tabel td { padding: 14px 10px; border-top: 1px solid rgba(42, 42, 92, 0.08); font-size: 13px; font-weight: 600; vertical-align: middle; }
    .tabel .r { text-align: right; white-space: nowrap; }
    .tabel .nama b { display: block; font-size: 13px; font-weight: 800; }
    .tabel .nama a { text-decoration: none; }
    .tabel .nama a:hover b { text-decoration: underline; }
    .tabel .nama span { display: block; margin-top: 2px; font-size: 11.5px; font-weight: 500; color: var(--muted); }

    .badge { display: inline-block; padding: 4px 11px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .badge.merah { background: var(--danger-bg); color: #a3153f; }
    .badge.kuning { background: var(--warn-bg); color: var(--warn-ink); }
    .badge.netral { background: #e8e7e3; color: var(--muted); }
    .badge.utang { background: #efe6fb; color: #6b2fa8; }
    .badge.piutang { background: #e4e9fb; color: #2f4aa8; }
    .badge.belum_lunas { background: var(--danger-bg); color: #a3153f; }
    .badge.dp { background: var(--warn-bg); color: var(--warn-ink); }
    .badge.cicilan { background: #e4e9fb; color: #2f4aa8; }
    .badge.lunas { background: #dff3e8; color: #1f6b44; }

    .kosong { padding: 34px 12px; text-align: center; font-size: 13px; font-weight: 500; color: var(--muted); }
    .kosong b { display: block; margin-bottom: 4px; font-size: 14px; font-weight: 800; color: var(--ink); }

    .sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }

    @media (max-width: 720px) {
        body { padding: 0; }
        .shell { border-radius: 0; padding: 12px; }
        .board { padding: 20px 16px 24px; border-radius: 24px; }
    }
</style>
