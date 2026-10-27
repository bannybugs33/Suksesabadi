<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Andi Saputra',
            'email' => 'andi@suksesabadi.com',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Selamat pagi, Pak Andi');
        $response->assertSee('Berikut kondisi utang–piutang Toko Tempo per 29 September 2026.');
        $response->assertSee('Andi Saputra');
        $response->assertSee('Peringatan jatuh tempo');
        $response->assertSee('Sisa piutang pelanggan');
        $response->assertSee('Sisa utang supplier');
        $response->assertSee('Arus utang & piutang', false);
        $response->assertSee('Status pembayaran');
        $response->assertSee('Perlu perhatian');
        $response->assertSee('Aktivitas terbaru');
    }

    public function test_dashboard_uses_real_time_current_date(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-18 14:30:00'));

        $user = User::factory()->create([
            'name' => 'Andi Saputra',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('per 18 Oktober 2026');
        $response->assertSee('18 Okt 2026, 14.30');
    }

    public function test_dashboard_displays_metrics_and_summary_cards(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Rp12.450.000');
        $response->assertSee('3 kuitansi pelanggan');
        $response->assertSee('Rp8.400.000');
        $response->assertSee('1 kuitansi supplier');
        $response->assertSee('4 kuitansi');
        $response->assertSee('3 terlambat • 1 besok');
        $response->assertSee('Rp15.000.000');
        $response->assertSee('1 pembayaran selesai');
        $response->assertSee('Rp45.850.000');
    }

    public function test_dashboard_displays_jatuh_tempo_warning_banner_and_perlu_perhatian(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('• Utang & Piutang', false);
        $response->assertSee('Lihat detail');
        $response->assertSee('Lihat semua');
        $response->assertSee(route('jatuh-tempo.index'));
        $response->assertSee(route('arsip-kuitansi.index'));
        $response->assertSee('Toko Makmur Jaya');
        $response->assertSee('PT Sumber Pangan');
        $response->assertSee('Warung Bu Sari');
        $response->assertSee('Kantin Melati');
        $response->assertSee('Tambah kuitansi');
        $response->assertSee('Kuitansi Baru');
    }

    public function test_dashboard_shows_zero_values_without_dummy_data_for_user_without_kuitansi(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('0 kuitansi pelanggan');
        $response->assertSee('0 kuitansi supplier');
        $response->assertSee('0 terlambat • 0 besok');
        $response->assertSee('Belum ada utang atau piutang yang mendekati jatuh tempo.');
        $response->assertDontSee('Rp24.450.000');
        $response->assertDontSee('detailPengingatPopup');
    }

    public function test_dashboard_popup_shows_most_urgent_reminder(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertSee('detailPengingatPopup');
        $response->assertSee('Terlambat 7 hari');
    }

    public function test_dashboard_shows_login_reminder_toast_from_session(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $response = $this->actingAs($user)
            ->withSession(['pengingat_login' => ['jumlah' => 4, 'terlambat' => 3, 'sisa' => 20850000.0]])
            ->get('/dashboard');

        $response->assertSee('toastPengingat');
        $response->assertSee('4 kuitansi perlu perhatian');
        $response->assertSee('Rp20.850.000 belum diselesaikan');
    }
}
