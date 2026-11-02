<?php

namespace Tests\Feature;

use App\Models\Kuitansi;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Fitur 7: pengingat jatuh tempo (H-7, hari ini, terlambat) untuk tagihan aktif yang masih bersisa.
 */
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

    private function buatKuitansi(User $owner, array $override = []): Kuitansi
    {
        return Kuitansi::create(array_merge([
            'user_id' => $owner->id,
            'nomor_kuitansi' => 'KWT-J-'.uniqid(),
            'nama_pihak' => 'Pihak Uji',
            'role_pihak' => 'Pelanggan',
            'jenis' => 'piutang',
            'nominal_transaksi' => 1000000,
            'status_pembayaran' => 'belum_lunas',
            'tanggal_kuitansi' => '2026-09-01',
            'tanggal_jatuh_tempo' => '2026-12-31',
        ], $override));
    }

    public function test_unauthenticated_user_cannot_access_jatuh_tempo(): void
    {
        $this->get('/jatuh-tempo')->assertRedirect(route('login'));
    }

    public function test_page_shows_title_summary_cards_tabs_and_table_columns(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/jatuh-tempo');

        $response->assertOk();
        $response->assertSee('Jatuh Tempo');
        $response->assertSee('Pantau tagihan yang perlu perhatian, rincian transaksi, dan bukti kuitansinya.');
        $response->assertSee('Sisa perlu diselesaikan');
        $response->assertSee('Piutang pelanggan');
        $response->assertSee('Utang ke supplier');
        $response->assertSee('Perlu perhatian');
        $response->assertSee('Pengingat aktif mulai H-7 sebelum jatuh tempo');
        $response->assertSee('Tidak ada tagihan yang perlu perhatian');
    }

    public function test_summary_and_rows_come_from_the_same_active_reminder_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->actingAs(User::query()->first())->get('/jatuh-tempo');

        $response->assertOk();
        $response->assertSee('Rp20.850.000');                 // sisa perlu diselesaikan
        $response->assertSee('Total sisa dari 4 kuitansi');
        $response->assertSee('Rp12.450.000');                 // piutang pelanggan
        $response->assertSee('Rp8.400.000');                  // utang ke supplier
        $response->assertSee('4 kuitansi');
        $response->assertSee('3 terlambat · 0 hari ini · 1 H-7');
        foreach (['Toko Makmur Jaya', 'PT Sumber Pangan', 'Warung Bu Sari', 'Kantin Melati'] as $nama) {
            $response->assertSee($nama);
        }
        $response->assertSee('Terlambat 7 hari');
        $response->assertSee('H-1');
        // Kuitansi lunas tidak ikut pengingat.
        $response->assertDontSee('Toko Berkah Jaya');
    }

    public function test_tabs_filter_by_category(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $terlambat = $this->actingAs($user)->get('/jatuh-tempo?tab=terlambat');
        $terlambat->assertSee('Toko Makmur Jaya');
        $terlambat->assertSee('PT Sumber Pangan');
        $terlambat->assertSee('Warung Bu Sari');
        $terlambat->assertDontSee('Kantin Melati');

        $mendekati = $this->actingAs($user)->get('/jatuh-tempo?tab=h-7');
        $mendekati->assertSee('Kantin Melati');
        $mendekati->assertDontSee('Toko Makmur Jaya');

        $hariIni = $this->actingAs($user)->get('/jatuh-tempo?tab=hari-ini');
        $hariIni->assertSee('Tidak ada tagihan pada kategori ini');
        $hariIni->assertDontSee('Kantin Melati');
    }

    public function test_example_from_system_analyst_20_november(): void
    {
        $owner = User::factory()->create();
        $this->buatKuitansi($owner, ['nama_pihak' => 'Tempo 20 Nov', 'tanggal_jatuh_tempo' => '2026-11-20']);

        // 12 November: belum diingatkan (H-8).
        Carbon::setTestNow(Carbon::parse('2026-11-12 08:00:00'));
        $this->actingAs($owner)->get('/jatuh-tempo')->assertDontSee('Tempo 20 Nov');

        // 13 November: mulai diingatkan (H-7).
        Carbon::setTestNow(Carbon::parse('2026-11-13 08:00:00'));
        $r = $this->actingAs($owner)->get('/jatuh-tempo');
        $r->assertSee('Tempo 20 Nov');
        $r->assertSee('H-7');

        // 20 November: jatuh tempo hari ini.
        Carbon::setTestNow(Carbon::parse('2026-11-20 08:00:00'));
        $r = $this->actingAs($owner)->get('/jatuh-tempo');
        $r->assertSee('Jatuh tempo hari ini');
        $r->assertSee('1 hari ini');

        // 21 November: terlambat satu hari.
        Carbon::setTestNow(Carbon::parse('2026-11-21 08:00:00'));
        $this->actingAs($owner)->get('/jatuh-tempo')->assertSee('Terlambat 1 hari');
    }

    public function test_only_active_invoices_with_remaining_balance_are_reminded(): void
    {
        $owner = User::factory()->create();
        $this->buatKuitansi($owner, ['nama_pihak' => 'Masih Bersisa', 'tanggal_jatuh_tempo' => '2026-10-01']);

        $sudahLunas = $this->buatKuitansi($owner, [
            'nama_pihak' => 'Sudah Lunas Dibayar', 'tanggal_jatuh_tempo' => '2026-10-01', 'status_pembayaran' => 'dp',
        ]);
        $sudahLunas->pembayaran()->create([
            'nominal' => 1000000, 'tanggal_pembayaran' => '2026-09-20', 'metode_pembayaran' => 'Tunai', 'dicatat_oleh' => 'Andi',
        ]);

        $this->buatKuitansi($owner, [
            'nama_pihak' => 'Status Lunas', 'tanggal_jatuh_tempo' => '2026-10-01', 'status_pembayaran' => 'lunas',
        ])->pembayaran()->create([
            'nominal' => 1000000, 'tanggal_pembayaran' => '2026-09-20', 'metode_pembayaran' => 'Tunai', 'dicatat_oleh' => 'Andi',
        ]);

        $response = $this->actingAs($owner)->get('/jatuh-tempo');

        $response->assertSee('Masih Bersisa');
        $response->assertDontSee('Sudah Lunas Dibayar');
        $response->assertDontSee('Status Lunas');
        $response->assertSee('Total sisa dari 1 kuitansi');
    }

    public function test_other_owner_data_is_never_shown(): void
    {
        $owner = User::factory()->create();
        $lain = User::factory()->create();
        $this->buatKuitansi($lain, ['nama_pihak' => 'Milik Orang Lain', 'tanggal_jatuh_tempo' => '2026-09-20']);

        $this->actingAs($owner)->get('/jatuh-tempo')->assertDontSee('Milik Orang Lain');
    }

    public function test_paying_in_full_removes_invoice_from_reminders(): void
    {
        $owner = User::factory()->create();
        $kuitansi = $this->buatKuitansi($owner, ['nama_pihak' => 'Akan Lunas', 'tanggal_jatuh_tempo' => '2026-10-02']);

        $this->actingAs($owner)->get('/jatuh-tempo')->assertSee('Akan Lunas');

        $this->actingAs($owner)->post(route('pembayaran.store', $kuitansi), [
            'nominal' => 1000000,
            'tanggal_pembayaran' => '2026-09-29',
            'metode_pembayaran' => 'Tunai',
        ])->assertRedirect();

        // Baris tagihan hilang dari daftar (pesan sukses sekali tampil boleh menyebut namanya).
        $this->actingAs($owner)->get('/jatuh-tempo')->assertDontSee('data-nama="Akan Lunas"', false);
        $this->actingAs($owner)->get('/jatuh-tempo')->assertSee('Tidak ada tagihan yang perlu perhatian');
        $this->actingAs($owner)->get('/dashboard')->assertDontSee('perlu perhatian mulai H-7');
    }

    public function test_partial_payment_keeps_invoice_with_reduced_balance_and_late_days(): void
    {
        $owner = User::factory()->create();
        $kuitansi = $this->buatKuitansi($owner, ['nama_pihak' => 'Dicicil', 'tanggal_jatuh_tempo' => '2026-09-26']);

        $this->actingAs($owner)->post(route('pembayaran.store', $kuitansi), [
            'nominal' => 400000,
            'tanggal_pembayaran' => '2026-09-29',
            'metode_pembayaran' => 'Transfer bank',
        ])->assertRedirect();

        $response = $this->actingAs($owner)->get('/jatuh-tempo');

        $response->assertSee('Dicicil');
        $response->assertSee('Rp600.000');       // sisa berkurang
        $response->assertSee('Rp400.000');       // terbayar
        $response->assertSee('Terlambat 3 hari'); // tetap tampil dengan jumlah hari keterlambatan
    }

    public function test_row_has_pay_button_with_modal_and_links_to_detail(): void
    {
        $owner = User::factory()->create();
        $kuitansi = $this->buatKuitansi($owner, ['nama_pihak' => 'Punya Tombol', 'tanggal_jatuh_tempo' => '2026-10-02']);

        $response = $this->actingAs($owner)->get('/jatuh-tempo');

        $response->assertSee('data-bayar', false);
        $response->assertSee('data-action="'.route('pembayaran.store', $kuitansi).'"', false);
        $response->assertSee('Tambah pembayaran');
        $response->assertSee('Simpan pembayaran');
        $response->assertSee('href="'.route('arsip-kuitansi.show', $kuitansi).'"', false);
    }

    public function test_failed_payment_reopens_the_modal_with_the_error(): void
    {
        $owner = User::factory()->create();
        $kuitansi = $this->buatKuitansi($owner, ['nama_pihak' => 'Salah Isi', 'tanggal_jatuh_tempo' => '2026-10-02']);

        $this->followingRedirects()->actingAs($owner)->from('/jatuh-tempo')->post(route('pembayaran.store', $kuitansi), [
            '_kuitansi' => $kuitansi->id,
            'nominal' => 9999999,
            'tanggal_pembayaran' => '2026-09-29',
            'metode_pembayaran' => 'Tunai',
        ])->assertSee('Nominal melebihi sisa tagihan')->assertSee('var bukaId = '.$kuitansi->id, false);
    }

    public function test_reminders_are_not_sent_by_whatsapp_or_email(): void
    {
        Mail::fake();
        Notification::fake();

        $this->seed(DatabaseSeeder::class);
        $owner = User::query()->first();
        $this->actingAs($owner)->get('/dashboard')->assertOk();
        $this->actingAs($owner)->get('/jatuh-tempo')->assertOk();

        Mail::assertNothingSent();
        Mail::assertNothingQueued();
        Notification::assertNothingSent();
    }

    public function test_dashboard_and_jatuh_tempo_count_the_same_reminders(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::query()->first();

        $this->actingAs($owner)->get('/dashboard')->assertSee('4 tagihan perlu perhatian mulai H-7 (3 terlambat)');
        $this->actingAs($owner)->get('/jatuh-tempo')->assertSee('3 terlambat · 0 hari ini · 1 H-7');
    }
}
