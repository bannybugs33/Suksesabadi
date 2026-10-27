{{-- Modal "Catat pembayaran" + notifikasi. Dipakai di jatuh-tempo/index.blade.php --}}
<style>
    .btn-catat-bayar {
        margin-top: 14px;
        width: 100%;
        padding: 11px 14px;
        border: none;
        border-radius: 8px;
        background: #91283b;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
    }
    .btn-catat-bayar:hover { background: #7a2030; }

    .pay-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1200;
        max-width: 380px;
        padding: 13px 16px;
        border-radius: 10px;
        background: #ebf5ee;
        color: #2e6930;
        border: 1px solid #c7e3cb;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .pay-overlay {
        position: fixed;
        inset: 0;
        z-index: 1100;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(20, 22, 34, 0.5);
    }
    .pay-overlay.is-open { display: flex; }
    .pay-dialog {
        width: 100%;
        max-width: 440px;
        max-height: 92vh;
        overflow-y: auto;
        padding: 22px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        font-family: inherit;
    }
    .pay-dialog h3 { margin: 0 0 4px; font-size: 17px; color: #1f2233; }
    .pay-summary {
        margin: 10px 0 16px;
        padding: 12px 14px;
        border-radius: 8px;
        background: #fafafc;
        font-size: 12.5px;
        color: #5b5e6e;
        line-height: 1.6;
    }
    .pay-summary strong { color: #1f2233; }
    .pay-field { margin-bottom: 13px; }
    .pay-field label {
        display: block;
        margin-bottom: 5px;
        font-size: 12px;
        font-weight: 700;
        color: #3a3d4e;
    }
    .pay-field input {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #d9dbe3;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        background: #fff;
        transition: all 0.15s ease;
    }
    .pay-field select {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 36px 10px 12px;
        border: 1px solid #d9dbe3;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        background-color: #fff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23555865' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 14px 14px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .pay-field input:hover,
    .pay-field select:hover {
        border-color: #b0b4be;
    }
    .pay-field input:focus,
    .pay-field select:focus {
        outline: none;
        border-color: #91283b;
        box-shadow: 0 0 0 3px rgba(145, 40, 59, 0.1);
    }
    .pay-row { display: flex; gap: 8px; }
    .pay-row input { flex: 1; }
    .pay-btn-sisa {
        padding: 0 12px;
        border: 1px solid #91283b;
        border-radius: 8px;
        background: #fff;
        color: #91283b;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        font-family: inherit;
    }
    .pay-btn-sisa:hover { background: #fbeff1; }
    .pay-errors {
        margin-bottom: 13px;
        padding: 10px 12px;
        border-radius: 8px;
        background: #fbe3e1;
        color: #af3833;
        font-size: 12.5px;
    }
    .pay-errors div + div { margin-top: 3px; }
    .pay-actions { display: flex; gap: 10px; margin-top: 18px; }
    .pay-actions button {
        flex: 1;
        padding: 11px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
    }
    .pay-btn-cancel { border: 1px solid #d9dbe3; background: #fff; color: #3a3d4e; }
    .pay-btn-save { border: none; background: #91283b; color: #fff; }
    .pay-btn-save:disabled { opacity: 0.6; cursor: default; }
</style>

@if (session('status'))
    <div class="pay-toast" id="payToast" role="status">{{ session('status') }}</div>
@endif

<div class="pay-overlay" id="payOverlay" onclick="if (event.target === this) closePaymentModal()">
    <div class="pay-dialog" role="dialog" aria-modal="true" aria-labelledby="payTitle">
        <h3 id="payTitle">Catat pembayaran</h3>

        <div class="pay-summary" id="paySummary"></div>

        @if ($errors->any())
            <div class="pay-errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="" id="payForm" onsubmit="payLockSubmit()">
            @csrf

            <div class="pay-field">
                <label for="payNominal">Nominal pembayaran (Rp)</label>
                <div class="pay-row">
                    <input type="number" name="nominal" id="payNominal" min="1" step="1"
                           value="{{ old('nominal') }}" placeholder="Contoh: 1000000" required>
                    <button type="button" class="pay-btn-sisa" onclick="payFillSisa()">Lunasi sisa</button>
                </div>
            </div>

            <div class="pay-field">
                <label for="payTanggal">Tanggal pembayaran</label>
                <input type="date" name="tanggal_pembayaran" id="payTanggal"
                       value="{{ old('tanggal_pembayaran', now()->format('Y-m-d')) }}"
                       max="{{ now()->format('Y-m-d') }}" required>
            </div>

            <div class="pay-field">
                <label for="payMetode">Metode pembayaran</label>
                <select name="metode_pembayaran" id="payMetode" required>
                    <option value="Tunai" {{ old('metode_pembayaran') === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="Transfer bank" {{ old('metode_pembayaran') === 'Transfer bank' ? 'selected' : '' }}>Transfer bank</option>
                </select>
            </div>

            <div class="pay-field">
                <label for="payCatatan">Catatan (opsional)</label>
                <input type="text" name="catatan" id="payCatatan" maxlength="255"
                       value="{{ old('catatan') }}" placeholder="Contoh: cicilan tahap 2">
            </div>

            <div class="pay-actions">
                <button type="button" class="pay-btn-cancel" onclick="closePaymentModal()">Batal</button>
                <button type="submit" class="pay-btn-save" id="paySaveBtn">Simpan pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
    let payCurrentId = null;

    function openPaymentModal(id) {
        const item = (typeof kuitansiData !== 'undefined') ? kuitansiData[id] : null;
        if (!item) return;

        payCurrentId = id;
        document.getElementById('payForm').action = item.pembayaran_url;
        document.getElementById('payNominal').max = item.sisa;

        const jenis = item.jenis === 'piutang' ? 'Piutang' : 'Utang';
        document.getElementById('paySummary').innerHTML =
            '<strong>' + item.nama_pihak + '</strong> • ' + item.nomor_kuitansi + '<br>' +
            jenis + ' • Total ' + item.nominal_transaksi_formatted + '<br>' +
            'Sudah dibayar ' + item.terbayar_formatted + ' • <strong>Sisa ' + item.sisa_formatted + '</strong>';

        document.getElementById('payOverlay').classList.add('is-open');
        document.getElementById('payNominal').focus();
    }

    function closePaymentModal() {
        document.getElementById('payOverlay').classList.remove('is-open');
    }

    function payFillSisa() {
        const item = kuitansiData[payCurrentId];
        if (item) document.getElementById('payNominal').value = Math.round(item.sisa);
    }

    function payLockSubmit() {
        const btn = document.getElementById('paySaveBtn');
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePaymentModal();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const toast = document.getElementById('payToast');
        if (toast) setTimeout(function () { toast.style.display = 'none'; }, 6000);

        @if ($errors->any() && $selectedKuitansi)
            openPaymentModal({{ $selectedKuitansi->id }});
        @endif
    });
</script>
