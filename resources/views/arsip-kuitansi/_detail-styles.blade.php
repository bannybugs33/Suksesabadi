<style>
    .kembali { display: inline-flex; align-items: center; height: 30px; padding: 0 14px; margin-bottom: 14px; border-radius: 999px; background: var(--field); color: var(--ink); font-size: 11.5px; font-weight: 700; text-decoration: none; }
    .kembali:hover { background: #fff; }

    .detail-head h1 { font-size: 22px; }
    .detail-head p { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .aksi-head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .btn-primary.kecil { height: 36px; padding: 0 18px; font-size: 12.5px; box-shadow: 0 10px 20px -12px rgba(42, 42, 92, 0.6); }
    .btn-primary.kecil:disabled, .btn-soft:disabled { opacity: 0.6; cursor: progress; }
    .btn-soft.bahaya, .mini.bahaya { color: var(--accent); }
    .btn-soft.bahaya:hover, .mini.bahaya:hover { background: var(--danger-bg); }

    .banner-batal { margin-top: 18px; display: flex; flex-direction: column; gap: 4px; padding: 14px 18px; border-radius: 18px; background: var(--danger-bg); color: #8f1b34; font-size: 12.5px; font-weight: 500; }
    .banner-batal b { font-size: 13px; font-weight: 800; }

    .ringkas { margin-top: 18px; display: flex; align-items: center; justify-content: space-between; gap: 8px 20px; flex-wrap: wrap; padding: 13px 18px; border-radius: 16px; background: var(--card); font-size: 12.5px; font-weight: 500; color: var(--ink); box-shadow: inset 0 2px 5px rgba(42, 42, 92, 0.05); }
    .ringkas b { font-weight: 800; }
    .tempo-ringkas { font-weight: 800; }
    .tempo-ringkas.merah { color: var(--accent); }
    .tempo-ringkas.kuning { color: var(--warn-ink); }
    .tempo-ringkas.lunas { color: #1f6b44; }
    .tempo-ringkas.netral { color: var(--muted); font-weight: 600; }

    .kolom { margin-top: 18px; display: grid; grid-template-columns: minmax(0, 1.75fr) minmax(0, 1fr); gap: 18px; align-items: start; }
    .kolom-kiri, .kolom-kanan { display: flex; flex-direction: column; gap: 18px; min-width: 0; }

    .kartu { background: var(--card); border-radius: 22px; padding: 20px 22px; box-shadow: 0 14px 26px -22px rgba(42, 42, 92, 0.45); }
    .kartu h2 { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; margin-bottom: 10px; font-size: 14px; font-weight: 800; letter-spacing: -0.1px; }
    .link-kecil { font-size: 11.5px; font-weight: 700; color: var(--accent); text-decoration: none; }
    .link-kecil:hover { text-decoration: underline; }

    .kv { display: flex; align-items: baseline; justify-content: space-between; gap: 16px; padding: 9px 0; border-top: 1px solid rgba(42, 42, 92, 0.07); }
    .kv:first-of-type { border-top: 0; }
    .kv span { font-size: 11.5px; font-weight: 500; color: var(--muted); flex-shrink: 0; }
    .kv b { font-size: 12px; font-weight: 800; text-align: right; overflow-wrap: anywhere; }

    .bayar { display: flex; align-items: center; gap: 10px; padding: 12px 0; border-top: 1px solid rgba(42, 42, 92, 0.07); }
    .bayar:first-of-type { border-top: 0; }
    .bayar-info { flex: 1; min-width: 0; }
    .bayar-info b { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 800; }
    .bayar-info small { display: block; margin-top: 3px; font-size: 11px; font-weight: 500; color: var(--muted); }
    .bayar-info .badge { padding: 2px 8px; font-size: 9.5px; }
    .alasan { color: var(--accent) !important; }
    .bayar-nominal { font-size: 12px; font-weight: 800; white-space: nowrap; }
    .bayar.is-batal { opacity: 0.62; }
    .bayar.is-batal .bayar-nominal { text-decoration: line-through; }

    .mini { height: 28px; padding: 0 12px; border: 0; border-radius: 999px; background: #fff; color: var(--ink); font: inherit; font-size: 11px; font-weight: 700; cursor: pointer; box-shadow: 0 5px 12px -8px rgba(42, 42, 92, 0.45); }
    .mini:hover { background: #f4f3f0; }

    .sisa { margin-top: 8px; display: flex; align-items: center; justify-content: space-between; padding: 11px 14px; border-radius: 12px; background: var(--field); font-size: 12px; font-weight: 700; }
    .sisa b { font-weight: 800; }

    .log { padding: 11px 0; border-top: 1px solid rgba(42, 42, 92, 0.07); display: flex; flex-direction: column; gap: 3px; font-size: 11.5px; font-weight: 500; color: var(--ink); }
    .log:first-of-type { border-top: 0; }
    .log b { font-size: 12px; font-weight: 800; }
    .log small { font-size: 10.5px; color: var(--muted); }
    .kosong-kecil, .kecil-muted, .hlp { font-size: 11px; font-weight: 500; color: var(--muted); }
    .kosong-kecil { padding: 4px 0 2px; }
    .kecil-muted { margin-top: 10px; }
    .hlp { margin-top: 6px; }

    .foto { display: block; width: 100%; aspect-ratio: 4 / 3; object-fit: contain; border-radius: 16px; background: var(--field); }
    .foto-kosong { display: flex; align-items: center; justify-content: center; }
    .unduh { margin-top: 12px; }

    /* ---------- Modal ---------- */
    dialog.modal { border: 0; padding: 0; margin: auto; background: transparent; width: min(480px, calc(100% - 28px)); max-height: calc(100% - 28px); overflow: visible; color: var(--ink); }
    dialog.modal::backdrop { background: rgba(35, 34, 79, 0.42); backdrop-filter: blur(2px); }
    dialog.modal[open] { animation: modal-masuk 0.16s ease-out; }
    @keyframes modal-masuk { from { opacity: 0; transform: translateY(8px) scale(0.985); } to { opacity: 1; transform: none; } }

    .modal-box { background: #fdfcfa; border-radius: 24px; padding: 22px 24px 20px; max-height: calc(100vh - 28px); overflow-y: auto; box-shadow: 0 40px 80px -30px rgba(42, 42, 92, 0.55); }
    .modal-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
    .modal-head h3 { font-size: 15px; font-weight: 800; letter-spacing: -0.1px; }
    .modal-head p { margin-top: 4px; font-size: 11.5px; font-weight: 500; line-height: 1.45; color: var(--muted); }
    .modal-x { width: 30px; height: 30px; flex-shrink: 0; border: 0; border-radius: 50%; background: #fff; color: var(--ink); font-size: 18px; line-height: 1; cursor: pointer; box-shadow: 0 5px 12px -8px rgba(42, 42, 92, 0.45); }
    .modal-x:hover { background: #f4f3f0; }

    .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0 12px; }
    .fld { margin-bottom: 13px; min-width: 0; }
    .fld label, .label-teks { display: block; margin-bottom: 6px; font-size: 11.5px; font-weight: 700; color: var(--ink); }
    .opsional { font-weight: 500; color: var(--muted); }

    .inp { width: 100%; min-height: 40px; padding: 0 14px; border: 1.5px solid transparent; border-radius: 14px; background: var(--field); box-shadow: inset 0 2px 5px rgba(42, 42, 92, 0.07); font: inherit; font-size: 12.5px; font-weight: 600; color: var(--ink-strong); outline: none; }
    textarea.inp { padding: 11px 14px; min-height: 72px; resize: vertical; line-height: 1.45; }
    .inp::placeholder { color: #a4a3b3; font-weight: 500; }
    .inp:focus { background: #fff; border-color: var(--ink); box-shadow: 0 0 0 4px rgba(42, 42, 92, 0.1); }
    select.inp { padding-right: 34px; cursor: pointer; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236b6b86' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; }
    input[type="file"].inp { padding: 9px 12px; font-size: 11.5px; }
    .fld .hlp { margin-top: 5px; }
    .err { margin-top: 5px; font-size: 11px; font-weight: 700; color: #a3153f; }

    .info { margin-bottom: 14px; padding: 11px 14px; border-radius: 14px; background: var(--field); font-size: 11.5px; font-weight: 600; }
    .peringatan { margin-bottom: 14px; padding: 11px 14px; border-radius: 14px; background: var(--warn-bg); color: var(--warn-ink); font-size: 11.5px; font-weight: 600; line-height: 1.45; }

    .chips { display: flex; flex-wrap: wrap; gap: 8px; }
    .chip { position: relative; cursor: pointer; }
    .chip input { position: absolute; opacity: 0; inset: 0; cursor: pointer; }
    .chip span { display: inline-flex; align-items: center; height: 32px; padding: 0 14px; border-radius: 999px; background: #fff; color: var(--ink); font-size: 11.5px; font-weight: 700; box-shadow: 0 5px 12px -8px rgba(42, 42, 92, 0.45); }
    .chip input:checked + span { background: var(--ink); color: #fff; }
    .chip input:focus-visible + span { outline: 3px solid rgba(42, 42, 92, 0.4); outline-offset: 2px; }

    .modal-foot { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 18px; }

    @media (max-width: 860px) {
        .kolom { grid-template-columns: 1fr; }
    }
    @media (max-width: 520px) {
        .grid2 { grid-template-columns: 1fr; }
        .bayar { flex-wrap: wrap; }
        .bayar-info { flex-basis: 100%; }
        .modal-box { padding: 18px 18px 16px; }
    }
    @media (prefers-reduced-motion: reduce) {
        dialog.modal[open] { animation: none; }
    }
</style>