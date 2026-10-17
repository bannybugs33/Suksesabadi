<?php

namespace Tests\Feature;

use App\Models\Kuitansi;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JatuhTempoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-09-29 09:42:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_unauthenticated_user_cannot_access_jatuh_tempo(): void
    {
        $response = $this->get('/jatuh-tempo');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_jatuh_tempo_page(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $response = $this->actingAs($user)->get('/jatuh-tempo');

        $response->assertStatus(200);
        $response->assertSee('Utang & Piutang — Jatuh Tempo', false);
        $response->assertSee('Pantau tagihan yang perlu perhatian, rincian transaksi, dan bukti kuitansinya.');
        $response->assertSee('Kuitansi perlu perhatian');
        $response->assertSee('• Utang & Piutang', false);
        $response->assertSee('Dashboard');
        $response->assertSee('Data tersinkron');
    }

    public function test_jatuh_tempo_renders_exact_mockup_metric_cards(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $response = $this->actingAs($user)->get('/jatuh-tempo');

        $response->assertStatus(200);
        // Sisa perlu diselesaikan
        $response->assertSee('Rp20.850.000');
        // Piutang pelanggan
        $response->assertSee('Rp12.450.000');
        // Utang ke supplier
        $response->assertSee('Rp8.400.000');
        // 4 kuitansi perlu perhatian (3 terlambat, 1 besok)
        $response->assertSee('4 kuitansi');
        $response->assertSee('3 terlambat • 1 jatuh tempo besok');
    }

    public function test_filter_by_tab_terlambat_and_h_1(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        // Test filter Terlambat
        $responseTerlambat = $this->actingAs($user)->get('/jatuh-tempo?tab=terlambat');
        $responseTerlambat->assertStatus(200);
        $responseTerlambat->assertSee('Toko Makmur Jaya');
        $responseTerlambat->assertSee('PT Sumber Pangan');
        $responseTerlambat->assertSee('Warung Bu Sari');
        $responseTerlambat->assertDontSee('Kantin Melati');

        // Test filter H-1
        $responseH1 = $this->actingAs($user)->get('/jatuh-tempo?tab=h-1');
        $responseH1->assertStatus(200);
        $responseH1->assertSee('Kantin Melati');
        $responseH1->assertDontSee('Toko Makmur Jaya');
    }

    public function test_filter_by_search_query(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $response = $this->actingAs($user)->get('/jatuh-tempo?q=Makmur');
        $response->assertStatus(200);
        $response->assertSee('Toko Makmur Jaya');
        $response->assertDontSee('Warung Bu Sari');
    }

    public function test_filter_by_jenis(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        // Hanya utang
        $responseUtang = $this->actingAs($user)->get('/jatuh-tempo?jenis=utang');
        $responseUtang->assertStatus(200);
        $responseUtang->assertSee('PT Sumber Pangan');
        $responseUtang->assertDontSee('Toko Makmur Jaya');

        // Hanya piutang
        $responsePiutang = $this->actingAs($user)->get('/jatuh-tempo?jenis=piutang');
        $responsePiutang->assertStatus(200);
        $responsePiutang->assertSee('Toko Makmur Jaya');
        $responsePiutang->assertDontSee('PT Sumber Pangan');
    }

    public function test_selected_kuitansi_displays_detail_and_payment_records(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        // Ketika belum ada yang dipilih, section detail tidak tampil sama sekali
        $responseUnselected = $this->actingAs($user)->get('/jatuh-tempo');
        $responseUnselected->assertStatus(200);
        $responseUnselected->assertDontSee('Detail transaksi');
        $responseUnselected->assertDontSee('DITAMPILKAN');

        // Ketika user memilih tempat/kuitansi (misal Warung Bu Sari)
        $warungBuSari = Kuitansi::where('nama_pihak', 'Warung Bu Sari')->first();
        $responseSelected = $this->actingAs($user)->get("/jatuh-tempo?selected={$warungBuSari->id}");
        $responseSelected->assertStatus(200);
        $responseSelected->assertSee('Detail transaksi');
        $responseSelected->assertSee('DITAMPILKAN');
        $responseSelected->assertSee('KWT-240918');
        $responseSelected->assertSee('Warung Bu Sari');
        $responseSelected->assertSee('Bukti kuitansi');
        $responseSelected->assertSee('kuitansi-warung-bu-sari.jpg');
        $responseSelected->assertSee('Riwayat pembayaran');
        $responseSelected->assertSee('Transfer bank');
        $responseSelected->assertSee('Tunai - pembayaran awal');
        $responseSelected->assertSee('Rp2.000.000');
        $responseSelected->assertSee('Rp3.000.000');
        $responseSelected->assertSee('Rp5.000.000');
    }

    public function test_dashboard_displays_jatuh_tempo_summary_card_and_link(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Peringatan jatuh tempo');
        $response->assertSee('Lihat detail');
        $response->assertSee(route('jatuh-tempo.index'));
        $response->assertSee('4 kuitansi');
        $response->assertSee('3 terlambat • 1 besok');
        $response->assertSee('Perlu perhatian');
        $response->assertSee('Toko Makmur Jaya');
    }

    public function test_jatuh_tempo_calculates_reminders_in_real_time(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-18 10:00:00'));
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $response = $this->actingAs($user)->get('/jatuh-tempo');

        $response->assertStatus(200);
        $response->assertSee('Data per 18 Okt 2026, 10.00');
        $response->assertSee('H-1 • Besok');
        $response->assertSee('Terlambat 7 hari');
    }

    public function test_selected_kuitansi_without_receipt_displays_empty_placeholder(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        // Kantin Melati tidak memiliki file bukti kuitansi
        $kantinMelati = Kuitansi::where('nama_pihak', 'Kantin Melati')->first();
        $this->assertNull($kantinMelati->bukti_kuitansi);

        $response = $this->actingAs($user)->get("/jatuh-tempo?selected={$kantinMelati->id}");

        $response->assertStatus(200);
        $response->assertSee('Detail transaksi');
        $response->assertSee('0 lampiran');
        $response->assertSee('Belum ada bukti kuitansi');
        $response->assertSee('Tidak ada lampiran berkas');
    }

    public function test_selected_kuitansi_with_custom_uploaded_receipt_displays_dynamic_image_and_name(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $tokoMakmur = Kuitansi::where('nama_pihak', 'Toko Makmur Jaya')->first();
        $tokoMakmur->update(['bukti_kuitansi' => 'kuitansi/struk-pembelian-makmur-999.png']);

        $response = $this->actingAs($user)->get("/jatuh-tempo?selected={$tokoMakmur->id}");

        $response->assertStatus(200);
        $response->assertSee('Detail transaksi');
        $response->assertSee('1 lampiran');
        $response->assertSee('struk-pembelian-makmur-999.png');
        $response->assertSee('storage/kuitansi/struk-pembelian-makmur-999.png');
    }
}
