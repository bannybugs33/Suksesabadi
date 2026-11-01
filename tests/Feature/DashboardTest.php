<?php

namespace Tests\Feature;

use App\Models\Kuitansi;
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

    private function owner(): User
    {
        return User::factory()->create(['name' => 'Andi Saputra', 'email' => 'andi@suksesabadi.com']);
    }

    private function buatKuitansi(User $owner, array $override = []): Kuitansi
    {
        return Kuitansi::create(array_merge([
            'user_id' => $owner->id,
            'nomor_kuitansi' => 'KWT-T-'.uniqid(),
            'nama_pihak' => 'Pihak Uji',
            'role_pihak' => 'Pelanggan',
            'jenis' => 'piutang',
            'nominal_transaksi' => 1000000,
            'status_pembayaran' => 'belum_lunas',
            'tanggal_kuitansi' => '2026-09-01',
            'tanggal_jatuh_tempo' => '2026-12-31',
        ], $override));
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_titles_that_explain_each_number(): void
    {
        $response = $this->actingAs($this->owner())->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Selamat pagi, Pak Andi');
        $response->assertSee('Kondisi utang-piutang Toko Sukses Abadi per 29 September 2026.');
        $response->assertSee('Posisi bersih toko');
        $response->assertSee('Sisa piutang dikurangi sisa utang');
        $response->assertSee('Sisa piutang');
        $response->assertSee('Belum dibayar pelanggan', false);
        $response->assertSee('Sisa utang');
        $response->assertSee('Belum dibayar toko', false);
        $response->assertSee('Dibayar bulan ini');
        $response->assertSee('Piutang masuk (dari pelanggan)', false);
        $response->assertSee('Utang keluar (ke supplier)', false);
        $response->assertSee('Status pembayaran');
        $response->assertSee('Perlu perhatian');
        $response->assertSee('Jatuh tempo terdekat');
        $response->assertSee('Aktivitas terbaru');
        $response->assertSee('Andi Saputra');
    }

    public function test_dashboard_uses_real_time_current_date_and_greeting(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-18 14:30:00'));

        $response = $this->actingAs($this->owner())->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Selamat siang, Pak Andi');
        $response->assertSee('per 18 Oktober 2026');
    }

    public function test_dashboard_numbers_come_from_database(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->actingAs(User::query()->first())->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Rp12.450.000');      // sisa piutang
        $response->assertSee('3 kuitansi');
        $response->assertSee('Rp8.400.000');       // sisa utang
        $response->assertSee('Rp4.050.000');       // posisi bersih = 12.450.000 - 8.400.000
        $response->assertSee('Rp45.850.000');      // total nilai tercatat
        $response->assertSee('Rp20.850.000');      // total belum dibayar
        $response->assertSee('4 tagihan perlu perhatian mulai H-7 (3 terlambat)');
    }

    public function test_dashboard_shows_zero_values_without_sample_data_for_user_without_kuitansi(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Rp0');
        $response->assertSee('Belum ada tagihan yang terlambat atau jatuh tempo dalam 7 hari ke depan.');
        $response->assertSee('Belum ada tagihan yang akan jatuh tempo.');
        $response->assertSee('Belum ada aktivitas.');
        $response->assertDontSee('Rp12.450.000');
        $response->assertDontSee('Rp45.850.000');
    }

    public function test_reminder_starts_at_h7_not_h8(): void
    {
        $owner = $this->owner();
        $this->buatKuitansi($owner, ['nama_pihak' => 'Tepat H-7', 'tanggal_jatuh_tempo' => '2026-10-06']);
        $this->buatKuitansi($owner, ['nama_pihak' => 'Masih H-8', 'tanggal_jatuh_tempo' => '2026-10-07']);

        $response = $this->actingAs($owner)->get('/dashboard');

        $response->assertSee('1 tagihan perlu perhatian mulai H-7 (0 terlambat)');
        $response->assertSeeInOrder(['Perlu perhatian', 'Tepat H-7', 'H-7']);
        // H-8 belum masuk pengingat, tetapi tetap muncul sebagai jatuh tempo terdekat.
        $response->assertSee('Masih H-8');
        $response->assertSee('8 hari lagi');
    }

    public function test_new_invoice_adds_to_remaining_balance_then_enters_reminder_at_h7(): void
    {
        $owner = $this->owner();
        $this->buatKuitansi($owner, [
            'nama_pihak' => 'Toko Baru', 'nominal_transaksi' => 2500000, 'tanggal_jatuh_tempo' => '2026-10-20',
        ]);

        // Masih jauh dari tempo: menambah sisa piutang, belum masuk pengingat.
        $response = $this->actingAs($owner)->get('/dashboard');
        $response->assertSee('Rp2.500.000');
        $response->assertSee('Belum ada tagihan yang terlambat atau jatuh tempo dalam 7 hari ke depan.');
        $response->assertDontSee('perlu perhatian mulai H-7');

        // Memasuki H-7: tagihan yang sama masuk pengingat.
        $response = $this->actingAs($owner)->get('/dashboard?tanggal_acuan=2026-10-13');
        $response->assertSee('1 tagihan perlu perhatian mulai H-7 (0 terlambat)');
        $response->assertSee('Toko Baru');
    }

    public function test_payment_reduces_remaining_balance_and_is_counted_by_payment_date(): void
    {
        $owner = $this->owner();
        $kuitansi = $this->buatKuitansi($owner, ['nama_pihak' => 'Pak Budi', 'nominal_transaksi' => 1000000]);

        $this->actingAs($owner)->post(route('pembayaran.store', $kuitansi), [
            'nominal' => 400000,
            'tanggal_pembayaran' => '2026-09-29',
            'metode_pembayaran' => 'Tunai',
        ])->assertRedirect();

        $response = $this->actingAs($owner)->get('/dashboard');

        $response->assertSee('Rp600.000');          // sisa piutang berkurang
        $response->assertSee('Rp400.000');          // dibayar bulan ini (tanggal pembayaran = bulan ini)
        $response->assertSee('Pembayaran Rp400.000 dicatat');
        $response->assertSee('Pak Budi • 29 Sep 2026');
    }

    public function test_fully_paid_invoice_leaves_balance_and_reminder_regardless_of_status_column(): void
    {
        $owner = $this->owner();
        $kuitansi = $this->buatKuitansi($owner, [
            'nama_pihak' => 'Sudah Beres', 'nominal_transaksi' => 500000, 'tanggal_jatuh_tempo' => '2026-09-20',
            'status_pembayaran' => 'dp', // kolom status sengaja tidak sinkron
        ]);
        $kuitansi->pembayaran()->create([
            'nominal' => 500000, 'tanggal_pembayaran' => '2026-09-25', 'metode_pembayaran' => 'Tunai', 'dicatat_oleh' => 'Andi',
        ]);

        $response = $this->actingAs($owner)->get('/dashboard');

        $response->assertDontSee('perlu perhatian mulai H-7');
        $response->assertDontSee('Terlambat 9 hari');
        $response->assertSee('Pengingat jatuh tempo: 0 tagihan perlu perhatian');
    }

    public function test_menus_lead_to_the_matching_features(): void
    {
        $response = $this->actingAs($this->owner())->get('/dashboard');

        $response->assertSee('href="'.route('arsip-kuitansi.index').'"', false);
        $response->assertSee('href="'.route('kuitansi.create').'"', false);
        $response->assertSee('href="'.route('jatuh-tempo.index').'"', false);
        $response->assertSee('href="'.route('rekapitulasi.index').'"', false);
        $response->assertSee('Tambah kuitansi');
        $response->assertSee('Keluar');
        $response->assertSee('action="'.route('logout').'"', false);
    }

    public function test_dashboard_shows_login_reminder_toast_from_session(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->actingAs(User::query()->first())
            ->withSession(['pengingat_login' => ['jumlah' => 4, 'terlambat' => 3, 'sisa' => 20850000.0]])
            ->get('/dashboard');

        $response->assertSee('toastPengingat');
        $response->assertSee('4 kuitansi perlu perhatian');
        $response->assertSee('Rp20.850.000 belum diselesaikan');
    }
}
