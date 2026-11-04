<?php

namespace Tests\Feature;

use App\Models\Kuitansi;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArsipKuitansiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/arsip-kuitansi');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_arsip_kuitansi(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->first();

        $response = $this->actingAs($user)->get('/arsip-kuitansi');

        $response->assertStatus(200);
        $response->assertSee('Arsip Kuitansi');
        $response->assertSee('Daftar kuitansi');
        $response->assertSee(route('dashboard'));
        $response->assertSee(route('arsip-kuitansi.index'));
        $response->assertSee(route('rekapitulasi.index'));
    }

    private function buatKuitansi(User $owner, array $override = []): Kuitansi
    {
        return Kuitansi::create(array_merge([
            'user_id' => $owner->id,
            'nomor_kuitansi' => 'KWT-A-' . uniqid(),
            'nama_pihak' => 'Pihak Uji',
            'role_pihak' => 'Pelanggan',
            'jenis' => 'piutang',
            'nominal_transaksi' => 1000000,
            'status_pembayaran' => 'belum_lunas',
            'tanggal_kuitansi' => '2026-09-01',
            'tanggal_jatuh_tempo' => '2026-12-31',
        ], $override));
    }

    public function test_page_shows_search_filters_and_table_columns(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->actingAs(User::query()->first())->get('/arsip-kuitansi');

        $response->assertOk();
        $response->assertSee('Arsip Kuitansi');
        $response->assertSee('Cari, periksa, dan kelola seluruh bukti transaksi utang-piutang.');
        $response->assertSee('Nama pelanggan/supplier atau nomor kuitansi');
        $response->assertSee('Hapus filter');
        $response->assertSee('Daftar kuitansi');
        $response->assertSee('5 data');
        $response->assertSee('Terbaru lebih dulu');
        $response->assertSee('Menampilkan 1–5 dari 5 kuitansi');
    }

    public function test_search_by_party_name_is_case_insensitive(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::query()->first();

        $response = $this->actingAs($owner)->get('/arsip-kuitansi?q=makmur');

        $response->assertSee('Toko Makmur Jaya');
        $response->assertDontSee('Warung Bu Sari');
        $response->assertSee('1 data');
    }

    public function test_search_by_receipt_number(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::query()->first();

        $response = $this->actingAs($owner)->get('/arsip-kuitansi?q=KWT-240915');

        $response->assertSee('PT Sumber Pangan');
        $response->assertDontSee('Toko Makmur Jaya');
    }

    public function test_search_ignores_surrounding_spaces(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->actingAs(User::query()->first())->get('/arsip-kuitansi?q=' . urlencode('  Kantin  '))
            ->assertSee('Kantin Melati')
            ->assertDontSee('Toko Makmur Jaya');
    }

    public function test_shows_data_not_found_message_when_nothing_matches(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->actingAs(User::query()->first())->get('/arsip-kuitansi?q=tidak-ada-pihak-ini');

        $response->assertOk();
        $response->assertSee('Data tidak ditemukan');
        $response->assertSee('0 data');
        $response->assertDontSee('Toko Makmur Jaya');
    }

    public function test_shows_data_not_found_for_owner_without_archive(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/arsip-kuitansi');

        $response->assertSee('Data tidak ditemukan');
        $response->assertSee('Belum ada arsip kuitansi');
    }

    public function test_filter_by_jenis_and_status_and_their_combination(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::query()->first();

        $utang = $this->actingAs($owner)->get('/arsip-kuitansi?jenis=utang');
        $utang->assertSee('PT Sumber Pangan');
        $utang->assertDontSee('Toko Makmur Jaya');

        $cicilan = $this->actingAs($owner)->get('/arsip-kuitansi?status=cicilan');
        $cicilan->assertSee('Warung Bu Sari');
        $cicilan->assertDontSee('Toko Makmur Jaya');

        $gabungan = $this->actingAs($owner)->get('/arsip-kuitansi?jenis=piutang&status=dp&q=Kantin');
        $gabungan->assertSee('Kantin Melati');
        $gabungan->assertDontSee('Warung Bu Sari');

        $kosong = $this->actingAs($owner)->get('/arsip-kuitansi?jenis=utang&status=lunas');
        $kosong->assertSee('Data tidak ditemukan');
    }

    public function test_results_are_paginated_five_per_page_and_keep_the_search(): void
    {
        $owner = User::factory()->create();
        foreach (range(1, 7) as $i) {
            $this->buatKuitansi($owner, [
                'nama_pihak' => "Pelanggan {$i}",
                'nomor_kuitansi' => "KWT-P-{$i}",
                'tanggal_kuitansi' => '2026-09-0' . $i,
            ]);
        }

        $hal1 = $this->actingAs($owner)->get('/arsip-kuitansi');
        $hal1->assertSee('7 data');
        $hal1->assertSee('Menampilkan 1–5 dari 7 kuitansi');
        $hal1->assertSee('Pelanggan 7');
        $hal1->assertDontSee('Pelanggan 1<', false);

        $hal2 = $this->actingAs($owner)->get('/arsip-kuitansi?page=2');
        $hal2->assertSee('Menampilkan 6–7 dari 7 kuitansi');

        $cari = $this->actingAs($owner)->get('/arsip-kuitansi?q=Pelanggan');
        $cari->assertSee('page=2', false);
        $cari->assertSee('q=Pelanggan', false);
    }

    public function test_sort_order_can_be_toggled(): void
    {
        $owner = User::factory()->create();
        $this->buatKuitansi($owner, ['nama_pihak' => 'Paling Lama', 'tanggal_kuitansi' => '2026-01-01']);
        $this->buatKuitansi($owner, ['nama_pihak' => 'Paling Baru', 'tanggal_kuitansi' => '2026-09-01']);

        $this->actingAs($owner)->get('/arsip-kuitansi')
            ->assertSeeInOrder(['Paling Baru', 'Paling Lama'])
            ->assertSee('Terbaru lebih dulu');

        $this->actingAs($owner)->get('/arsip-kuitansi?urut=terlama')
            ->assertSeeInOrder(['Paling Lama', 'Paling Baru'])
            ->assertSee('Terlama lebih dulu');
    }

    public function test_only_own_archive_is_searchable(): void
    {
        $owner = User::factory()->create();
        $lain = User::factory()->create();
        $this->buatKuitansi($lain, ['nama_pihak' => 'Rahasia Orang Lain']);

        $this->actingAs($owner)->get('/arsip-kuitansi?q=Rahasia')
            ->assertSee('Data tidak ditemukan')
            ->assertDontSee('Rahasia Orang Lain');
    }

    public function test_list_and_detail_show_the_same_data_for_the_same_receipt(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-29 09:00:00'));
        $owner = User::factory()->create();
        $kuitansi = $this->buatKuitansi($owner, [
            'nama_pihak' => 'Warung Sama',
            'nomor_kuitansi' => 'KWT-SAMA-1',
            'status_pembayaran' => 'dp',
            'nominal_transaksi' => 8750000,
            'tanggal_jatuh_tempo' => '2026-10-03',
        ]);
        $kuitansi->pembayaran()->create([
            'nominal' => 5000000,
            'tanggal_pembayaran' => '2026-09-10',
            'metode_pembayaran' => 'Tunai',
            'dicatat_oleh' => 'Andi',
        ]);

        $daftar = $this->actingAs($owner)->get('/arsip-kuitansi?q=KWT-SAMA-1');
        $daftar->assertSee('Warung Sama');
        $daftar->assertSee('Rp8.750.000');
        $daftar->assertSee('>DP<', false);          // label status sama dengan desain dan detail
        $daftar->assertSee('H-4');                    // penanda tempo dari aturan bersama
        $daftar->assertSee('href="' . route('arsip-kuitansi.show', $kuitansi) . '"', false);

        $detail = $this->actingAs($owner)->get(route('arsip-kuitansi.show', $kuitansi));
        $detail->assertOk();
        $detail->assertSee('Warung Sama');
        $detail->assertSee('Rp8.750.000');
        $detail->assertSee('Rp3.750.000');            // sisa
        $detail->assertSee('DP');
        $detail->assertDontSee('>Dp<', false);

        Carbon::setTestNow();
    }

    public function test_due_date_marker_only_for_active_invoices(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-29 09:00:00'));
        $owner = User::factory()->create();
        $this->buatKuitansi($owner, ['nama_pihak' => 'Telat Bayar', 'tanggal_jatuh_tempo' => '2026-09-26']);
        $lunas = $this->buatKuitansi($owner, [
            'nama_pihak' => 'Sudah Selesai',
            'status_pembayaran' => 'lunas',
            'tanggal_jatuh_tempo' => '2026-09-26',
        ]);
        $lunas->pembayaran()->create([
            'nominal' => 1000000,
            'tanggal_pembayaran' => '2026-09-20',
            'metode_pembayaran' => 'Tunai',
            'dicatat_oleh' => 'Andi',
        ]);

        $response = $this->actingAs($owner)->get('/arsip-kuitansi');

        $response->assertSee('Terlambat 3 hari');
        $this->assertSame(1, substr_count($response->getContent(), 'Terlambat 3 hari'));

        Carbon::setTestNow();
    }

    public function test_top_navigation_leads_to_the_matching_features(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/arsip-kuitansi');

        $response->assertSee('href="' . route('dashboard') . '"', false);
        $response->assertSee('href="' . route('kuitansi.create') . '"', false);
        $response->assertSee('href="' . route('jatuh-tempo.index') . '"', false);
        $response->assertSee('href="' . route('rekapitulasi.index') . '"', false);
        // Tab aktif kini berisi ikon SVG, lalu teks label di dalam <span>.
        $this->assertMatchesRegularExpression('/aria-current="page"\s*>\s*<svg.*?<\/svg>\s*<span>Arsip<\/span>/s', $response->getContent());
    }
}
