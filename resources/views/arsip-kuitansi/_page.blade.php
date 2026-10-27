<style>
    .main-content { margin-left: 220px; padding: 28px 32px; min-height: 100vh; }
    .page-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 16px; }
    .page-title { font-size: 24px; font-weight: 500; }
    .page-subtitle { color: #777; font-size: 13px; margin-top: 4px; }
    .card { background: #fff; border: 1px solid #ececf0; border-radius: 10px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.05); margin-bottom: 14px; }
    .btn { display: inline-block; border: 1px solid #e0e0e6; background: #fff; color: #292930; border-radius: 6px; padding: 8px 14px; font-size: 12px; cursor: pointer; text-decoration: none; }
    .btn-primary { background: #c93548; border-color: #c93548; color: #fff; font-weight: 600; }
    .field { border: 1px solid #d8dbe2; border-radius: 8px; padding: 9px 12px; font-size: 12px; background: #fff; width: 100%; transition: all 0.15s ease; }
    .field:focus { outline: none; border-color: #91283b; box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.1); }
    select.field, select.field-select {
        border: 1px solid #d8dbe2;
        border-radius: 8px;
        padding: 9px 32px 9px 12px;
        font-size: 12px;
        background-color: #ffffff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23555865' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 14px 14px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        cursor: pointer;
        color: #292930;
        transition: all 0.15s ease;
    }
    select.field:hover, select.field-select:hover { border-color: #b0b4be; background-color: #fafafc; }
    select.field:focus, select.field-select:focus { outline: none; border-color: #91283b; box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.1); }
    .filter-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-top: 10px; }
    .filter-row .field-select { width: auto; }
    .filter-label { color: #777; font-size: 11px; }
    .link-red { color: #c93548; font-size: 12px; text-decoration: none; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th { font-size: 10px; color: #777; text-align: left; padding: 8px 6px; font-weight: 500; border-bottom: 1px solid #ececf0; }
    td { padding: 13px 6px; border-bottom: 1px solid #f0f0f3; vertical-align: top; }
    tr.row-link { cursor: pointer; } tr.row-link:hover { background: #fafafb; }
    td small { display: block; color: #777; font-size: 11px; margin-top: 2px; }
    .num { text-align: right; font-weight: 600; }
    .badge { border-radius: 10px; padding: 2px 9px; font-size: 11px; font-weight: 600; display: inline-block; }
    .badge.cicilan { background: #e3f0fb; color: #2b6cb0; } .badge.dp { background: #fde4e4; color: #c0392b; }
    .badge.belum_lunas { background: #fdeede; color: #b45f06; } .badge.lunas { background: #e0f5e6; color: #238a4a; }
    .late { color: #c93548; font-size: 11px; display: block; }
    .alert-ok { background: #e0f5e6; color: #1f7a3f; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; font-size: 13px; }
    .empty { text-align: center; padding: 60px 20px; color: #777; } .empty b { display: block; color: #292930; font-size: 14px; margin-bottom: 4px; }
    .pager { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; font-size: 12px; color: #777; }
    .pager a, .pager span.cur { min-width: 26px; height: 26px; display: inline-grid; place-items: center; border: 1px solid #e0e0e6; border-radius: 4px; margin-left: 4px; text-decoration: none; color: #292930; }
    .pager span.cur { background: #c93548; border-color: #c93548; color: #fff; }
    .back { font-size: 12px; color: #777; text-decoration: none; display: inline-block; margin-bottom: 10px; }
    .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
    .stats .card { margin: 0; display: flex; justify-content: space-between; align-items: center; }
    .stats small { font-size: 10px; color: #777; display: block; margin-bottom: 4px; }
    .cols { display: grid; grid-template-columns: 1.25fr 1fr; gap: 14px; align-items: start; }
    h3 { font-size: 14px; font-weight: 600; margin-bottom: 10px; display: flex; justify-content: space-between; }
    .kv { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; } .kv span { color: #777; } .kv b { font-weight: 600; }
    .note { border-top: 1px solid #ececf0; margin-top: 8px; padding-top: 10px; font-size: 12px; line-height: 1.5; } .note span { color: #777; display: block; margin-bottom: 3px; }
    .pay { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f0f0f3; align-items: center; font-size: 12px; }
    .pay .ck { width: 22px; height: 22px; border-radius: 50%; background: #eee; display: grid; place-items: center; font-size: 11px; }
    .pay > div { flex: 1; } .pay small { display: block; color: #777; font-size: 11px; }
    .sisa { background: #f3f3f5; border-radius: 6px; padding: 10px; display: flex; justify-content: space-between; margin-top: 10px; font-size: 12px; }
    .photo { width: 100%; max-height: 260px; object-fit: contain; border-radius: 6px; background: #f3f3f5; }
    .fld { margin-bottom: 12px; } .fld label { display: block; font-size: 11px; font-weight: 600; margin-bottom: 4px; }
    .hlp { font-size: 11px; color: #777; margin-top: 3px; } .err { font-size: 11px; color: #c93548; margin-top: 3px; }
    .sum { border-top: 1px solid #ececf0; padding-top: 8px; margin-bottom: 12px; font-size: 12px; } .sum .kv { padding: 2px 0; }
    @media (max-width: 900px) { .sidebar { display: none; } .main-content { margin-left: 0; } .cols, .stats { grid-template-columns: 1fr; } }
</style>
