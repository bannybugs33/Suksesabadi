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
        $response->assertSee('Rp24.450.000');
        $response->assertSee('5 kuitansi pelanggan');
        $response->assertSee('Rp20.800.000');
        $response->assertSee('3 kuitansi supplier');
        $response->assertSee('4 kuitansi');
        $response->assertSee('3 terlambat • 1 besok');
        $response->assertSee('Rp18.200.000');
        $response->assertSee('7 pembayaran selesai');
        $response->assertSee('Rp63.450.000');
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
}
