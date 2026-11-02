<?php

namespace Tests\Feature;

use App\Models\Kuitansi;
use App\Models\Pembayaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Fitur 2: mengarsipkan kuitansi (satu foto, nomor arsip otomatis, pembayaran awal, jatuh tempo).
 */
class KuitansiBaruTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-30 09:00:00'));
        Storage::fake('local');
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function foto(string $nama = 'struk.jpg', string $mime = 'image/jpeg', int $kb = 100): UploadedFile
    {
        return UploadedFile::fake()->create($nama, $kb, $mime);
    }

    /** Data minimal yang valid: belum ada pembayaran, jatuh tempo diisi, satu foto. */
    private function data(array $override = []): array
    {
        return array_merge([
            'tanggal_kuitansi' => '2026-10-30',
            'nama_pihak' => 'Warung Baru',
            'jenis' => 'piutang',
            'nominal_transaksi' => '1000000',
            'tanggal_jatuh_tempo' => '2026-11-20',
            'bukti' => $this->foto(),
        ], $override);
    }

    private function simpan(User $owner, array $override = [])
    {
        return $this->actingAs($owner)->from('/kuitansi/baru')->post('/kuitansi/baru', $this->data($override));
    }

    public function test_unauthenticated_user_cannot_access_kuitansi_baru(): void
    {
        $this->get('/kuitansi/baru')->assertRedirect(route('login'));
        $this->post('/kuitansi/baru', [])->assertRedirect(route('login'));
    }

    public function test_form_shows_the_sections_and_no_manual_status_choice(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/kuitansi/baru');

        $response->assertOk();
        $response->assertSee('Tambah Kuitansi Baru');
        $response->assertSee('Informasi kuitansi');
        $response->assertSee('Pembayaran &amp; jatuh tempo', false);
        $response->assertSee('Nomor kuitansi fisik');
        $response->assertSee('Unggah foto kuitansi');
        $response->assertSee('JPG atau PNG hingga 10 MB');
        $response->assertSee('Ringkasan arsip');
        $response->assertSee('KWT-261030');                       // nomor arsip sementara
        $response->assertSee('menampilkan pengingat mulai H-7');
        $response->assertSee('name="nominal_dibayar"', false);
        $response->assertDontSee('name="status_pembayaran"', false);
        $response->assertDontSee('pengingatSelect', false);
        $response->assertDontSee('application/pdf', false);
        $response->assertDontSee('multiple', false);
    }

    // ---------------------------------------------------------------- foto

    public function test_exactly_one_photo_is_required(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['bukti' => null])->assertSessionHasErrors(['bukti' => 'Foto kuitansi wajib diunggah (tepat satu foto).']);
        $this->assertDatabaseCount('kuitansi', 0);
    }

    public function test_pdf_is_rejected(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['bukti' => $this->foto('kuitansi.pdf', 'application/pdf')])
            ->assertSessionHasErrors(['bukti' => 'Foto harus berformat JPG atau PNG. PDF tidak diterima.']);
        $this->assertDatabaseCount('kuitansi', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_other_formats_are_rejected_but_jpg_and_png_are_accepted(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['bukti' => $this->foto('a.gif', 'image/gif')])->assertSessionHasErrors('bukti');
        $this->simpan($owner, ['bukti' => $this->foto('a.webp', 'image/webp')])->assertSessionHasErrors('bukti');
        $this->assertDatabaseCount('kuitansi', 0);

        $this->simpan($owner, ['nama_pihak' => 'Foto Png', 'bukti' => $this->foto('a.png', 'image/png')])->assertSessionDoesntHaveErrors();
        $this->simpan($owner, ['nama_pihak' => 'Foto Jpeg', 'nominal_transaksi' => '2000000', 'bukti' => $this->foto('a.jpeg', 'image/jpeg')])->assertSessionDoesntHaveErrors();
        $this->assertDatabaseCount('kuitansi', 2);
    }

    public function test_several_photos_in_one_archive_are_rejected(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['bukti' => [$this->foto('1.jpg'), $this->foto('2.jpg')]])
            ->assertSessionHasErrors('bukti');

        // Lampiran tambahan di luar kolom foto juga ditolak.
        $this->simpan($owner, ['lampiran' => $this->foto('halaman2.jpg')])
            ->assertSessionHasErrors(['bukti' => 'Satu arsip hanya boleh memiliki satu foto. Simpan foto lain sebagai arsip terpisah bila memang transaksi berbeda.']);

        $this->assertDatabaseCount('kuitansi', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_photo_larger_than_10_mb_is_rejected(): void
    {
        $this->simpan(User::factory()->create(), ['bukti' => $this->foto('besar.jpg', 'image/jpeg', 10241)])
            ->assertSessionHasErrors(['bukti' => 'Ukuran foto maksimal 10 MB.']);
    }

    public function test_the_single_photo_is_stored_privately(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner)->assertSessionDoesntHaveErrors();

        $kuitansi = Kuitansi::firstOrFail();
        Storage::disk('local')->assertExists($kuitansi->bukti_kuitansi);
        Storage::disk('public')->assertMissing($kuitansi->bukti_kuitansi);
        $this->assertCount(1, Storage::disk('local')->allFiles());
    }

    // ---------------------------------------------------------------- nomor

    public function test_archive_number_is_generated_by_the_system_and_unique(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['nama_pihak' => 'A'])->assertSessionDoesntHaveErrors();
        $this->simpan($owner, ['nama_pihak' => 'B', 'nominal_transaksi' => '2000000'])->assertSessionDoesntHaveErrors();

        $this->assertSame(['KWT-261030', 'KWT-261030-2'], Kuitansi::orderBy('id')->pluck('nomor_kuitansi')->all());
    }

    public function test_archive_number_cannot_be_chosen_by_the_user(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['nomor_kuitansi' => 'BEBAS-123', 'nomor_arsip' => 'BEBAS-456']);

        $this->assertSame('KWT-261030', Kuitansi::firstOrFail()->nomor_kuitansi);
    }

    public function test_physical_receipt_number_is_optional_and_separate_from_archive_number(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['nama_pihak' => 'Tanpa Nomor Fisik']);
        $this->simpan($owner, ['nama_pihak' => 'Dengan Nomor Fisik', 'nominal_transaksi' => '2000000', 'nomor_kuitansi_fisik' => '  INV/2026/0042  ']);

        $tanpa = Kuitansi::where('nama_pihak', 'Tanpa Nomor Fisik')->firstOrFail();
        $dengan = Kuitansi::where('nama_pihak', 'Dengan Nomor Fisik')->firstOrFail();

        $this->assertNull($tanpa->nomor_kuitansi_fisik);
        $this->assertSame('INV/2026/0042', $dengan->nomor_kuitansi_fisik);
        $this->assertNotSame($dengan->nomor_kuitansi, $dengan->nomor_kuitansi_fisik);
        $this->assertStringStartsWith('KWT-', $dengan->nomor_kuitansi);
    }

    public function test_the_same_physical_number_may_exist_on_different_archives(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['nama_pihak' => 'Toko X', 'nomor_kuitansi_fisik' => '001'])->assertSessionDoesntHaveErrors();
        $this->simpan($owner, ['nama_pihak' => 'Toko Y', 'nomor_kuitansi_fisik' => '001'])->assertSessionDoesntHaveErrors();

        $this->assertSame(2, Kuitansi::where('nomor_kuitansi_fisik', '001')->count());
    }

    public function test_next_archive_number_continues_after_the_highest_suffix(): void
    {
        $owner = User::factory()->create();
        foreach (['KWT-261030', 'KWT-261030-3'] as $nomor) {
            Kuitansi::create([
                'user_id' => $owner->id, 'nomor_kuitansi' => $nomor, 'nama_pihak' => $nomor, 'jenis' => 'piutang',
                'nominal_transaksi' => 1, 'status_pembayaran' => 'belum_lunas', 'tanggal_kuitansi' => '2026-10-30',
            ]);
        }

        $this->assertSame('KWT-261030-4', Kuitansi::nomorArsipBerikut(Carbon::parse('2026-10-30')));
        $this->assertSame('KWT-261031', Kuitansi::nomorArsipBerikut(Carbon::parse('2026-10-31')));
    }

    public function test_temporary_number_endpoint_follows_the_receipt_date(): void
    {
        $owner = User::factory()->create();
        $this->simpan($owner);

        $this->actingAs($owner)->getJson('/kuitansi/nomor-sementara?tanggal=2026-10-30')->assertJson(['nomor' => 'KWT-261030-2']);
        $this->actingAs($owner)->getJson('/kuitansi/nomor-sementara?tanggal=2026-08-28')->assertJson(['nomor' => 'KWT-260828']);
    }

    // ---------------------------------------------------------------- pembayaran awal

    public function test_without_payment_the_status_is_belum_lunas_and_no_payment_is_recorded(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner)->assertSessionDoesntHaveErrors();

        $kuitansi = Kuitansi::firstOrFail();
        $this->assertSame('belum_lunas', $kuitansi->status_pembayaran);
        $this->assertSame(0, Pembayaran::count());
        $this->assertSame('2026-11-20', $kuitansi->tanggal_jatuh_tempo->toDateString());
    }

    public function test_status_cannot_be_chosen_manually(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['status_pembayaran' => 'lunas'])->assertSessionDoesntHaveErrors();

        $this->assertSame('belum_lunas', Kuitansi::firstOrFail()->status_pembayaran);
    }

    public function test_initial_payment_uses_the_real_payment_date_and_method(): void
    {
        $owner = User::factory()->create(['name' => 'Andi Saputra']);

        $this->simpan($owner, [
            'nominal_transaksi' => '8.750.000',
            'nominal_dibayar' => '3.000.000',
            'tanggal_pembayaran' => '2026-10-02',
            'metode_pembayaran' => 'Transfer bank',
        ])->assertSessionDoesntHaveErrors();

        $kuitansi = Kuitansi::firstOrFail();
        $pembayaran = $kuitansi->pembayaran()->firstOrFail();

        $this->assertSame('dp', $kuitansi->status_pembayaran);
        $this->assertSame(8750000.0, (float) $kuitansi->nominal_transaksi);
        $this->assertSame(3000000.0, (float) $pembayaran->nominal);
        $this->assertSame('2026-10-02', $pembayaran->tanggal_pembayaran->toDateString()); // bukan tanggal kuitansi
        $this->assertSame('Transfer bank', $pembayaran->metode_pembayaran);
        $this->assertSame('Andi Saputra', $pembayaran->dicatat_oleh);
    }

    public function test_initial_payment_requires_date_and_method_like_later_payments(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['nominal_dibayar' => '300000'])
            ->assertSessionHasErrors(['tanggal_pembayaran', 'metode_pembayaran']);

        $this->simpan($owner, ['nominal_dibayar' => '300000', 'tanggal_pembayaran' => '2026-10-30', 'metode_pembayaran' => 'Kartu'])
            ->assertSessionHasErrors('metode_pembayaran');

        $this->simpan($owner, ['nominal_dibayar' => '300000', 'tanggal_pembayaran' => '2026-10-31', 'metode_pembayaran' => 'Tunai'])
            ->assertSessionHasErrors(['tanggal_pembayaran' => 'Tanggal pembayaran tidak boleh di masa depan.']);

        $this->assertDatabaseCount('kuitansi', 0);
    }

    public function test_initial_payment_cannot_exceed_the_transaction_and_leaves_no_orphan_file(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, [
            'nominal_dibayar' => '1500000', 'tanggal_pembayaran' => '2026-10-30', 'metode_pembayaran' => 'Tunai',
        ])->assertSessionHasErrors(['nominal_dibayar' => 'Nominal dibayar tidak boleh melebihi nominal transaksi.']);

        $this->assertDatabaseCount('kuitansi', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_initial_status_follows_the_same_rule_as_later_payments(): void
    {
        $owner = User::factory()->create();
        $this->simpan($owner, [
            'nominal_transaksi' => '10000000', 'nominal_dibayar' => '2000000',
            'tanggal_pembayaran' => '2026-10-30', 'metode_pembayaran' => 'Tunai',
        ])->assertSessionDoesntHaveErrors();

        $kuitansi = Kuitansi::firstOrFail();
        $this->assertSame('dp', $kuitansi->status_pembayaran);               // 1 pembayaran -> DP

        $this->actingAs($owner)->post(route('pembayaran.store', $kuitansi), [
            'nominal' => 1000000, 'tanggal_pembayaran' => '2026-10-30', 'metode_pembayaran' => 'Tunai',
        ]);
        $this->assertSame('cicilan', $kuitansi->fresh()->status_pembayaran);  // 2 pembayaran -> cicilan

        $this->actingAs($owner)->post(route('pembayaran.store', $kuitansi), [
            'nominal' => 7000000, 'tanggal_pembayaran' => '2026-10-30', 'metode_pembayaran' => 'Tunai',
        ]);
        $this->assertSame('lunas', $kuitansi->fresh()->status_pembayaran);
    }

    // ---------------------------------------------------------------- jatuh tempo

    public function test_due_date_is_required_while_balance_remains(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, ['tanggal_jatuh_tempo' => null])
            ->assertSessionHasErrors(['tanggal_jatuh_tempo' => 'Tanggal jatuh tempo wajib diisi karena transaksi masih memiliki sisa.']);

        $this->simpan($owner, [
            'tanggal_jatuh_tempo' => null, 'nominal_dibayar' => '400000',
            'tanggal_pembayaran' => '2026-10-30', 'metode_pembayaran' => 'Tunai',
        ])->assertSessionHasErrors('tanggal_jatuh_tempo');

        $this->assertDatabaseCount('kuitansi', 0);
    }

    public function test_immediately_paid_transaction_can_be_archived_without_due_date(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, [
            'tanggal_jatuh_tempo' => null, 'nominal_dibayar' => '1.000.000',
            'tanggal_pembayaran' => '2026-10-30', 'metode_pembayaran' => 'Tunai',
        ])->assertSessionDoesntHaveErrors();

        $kuitansi = Kuitansi::firstOrFail();
        $this->assertSame('lunas', $kuitansi->status_pembayaran);
        $this->assertNull($kuitansi->tanggal_jatuh_tempo);
        $this->assertSame(1, $kuitansi->pembayaran()->count());

        // Tidak masuk pengingat dan tidak menambah sisa.
        $this->actingAs($owner)->get('/jatuh-tempo')->assertDontSee('data-nama="Warung Baru"', false);
        $this->actingAs($owner)->get('/arsip-kuitansi/'.$kuitansi->id)->assertOk();
    }

    public function test_a_due_date_entered_for_a_paid_off_transaction_is_ignored(): void
    {
        $owner = User::factory()->create();

        $this->simpan($owner, [
            'tanggal_jatuh_tempo' => '2026-11-20', 'nominal_dibayar' => '1000000',
            'tanggal_pembayaran' => '2026-10-30', 'metode_pembayaran' => 'Tunai',
        ])->assertSessionDoesntHaveErrors();

        $this->assertNull(Kuitansi::firstOrFail()->tanggal_jatuh_tempo);
    }

    // ---------------------------------------------------------------- validasi dasar & integrasi

    public function test_required_fields_are_validated(): void
    {
        $this->actingAs(User::factory()->create())->from('/kuitansi/baru')->post('/kuitansi/baru', [])
            ->assertSessionHasErrors(['tanggal_kuitansi', 'nama_pihak', 'jenis', 'nominal_transaksi', 'bukti']);
    }

    public function test_saved_archive_feeds_arsip_pembayaran_pengingat_and_rekap(): void
    {
        $owner = User::factory()->create();
        $this->simpan($owner, [
            'nama_pihak' => 'Terhubung Penuh', 'nominal_transaksi' => '5.000.000', 'nominal_dibayar' => '2.000.000',
            'tanggal_pembayaran' => '2026-10-30', 'metode_pembayaran' => 'Transfer bank', 'tanggal_jatuh_tempo' => '2026-11-05',
        ])->assertSessionDoesntHaveErrors();

        $this->actingAs($owner)->get('/arsip-kuitansi')->assertSee('Terhubung Penuh')->assertSee('>DP<', false);
        $this->actingAs($owner)->get('/jatuh-tempo')->assertSee('Terhubung Penuh')->assertSee('Rp3.000.000')->assertSee('H-6');
        $this->actingAs($owner)->get('/dashboard')->assertSee('Rp3.000.000');
        $this->actingAs($owner)->get('/rekapitulasi')->assertSee('Terhubung Penuh');
    }

    public function test_redirects_to_the_saved_archive_detail_with_a_success_message(): void
    {
        $owner = User::factory()->create();

        $response = $this->simpan($owner);

        $kuitansi = Kuitansi::firstOrFail();
        $response->assertRedirect(route('arsip-kuitansi.show', $kuitansi->id));
        $response->assertSessionHas('status');

        // Detail menampilkan konfirmasi beserta nomor arsip yang dibuat sistem.
        $this->actingAs($owner)->get(route('arsip-kuitansi.show', $kuitansi->id))
            ->assertOk()
            ->assertSee('Arsip KWT-261030 (Warung Baru) berhasil disimpan!');
    }

    public function test_photo_is_removed_if_saving_fails(): void
    {
        $owner = User::factory()->create();
        Kuitansi::creating(function () {
            throw new \RuntimeException('simulasi kegagalan database');
        });
        $this->withoutExceptionHandling();

        try {
            $this->simpan($owner);
            $this->fail('Pengecualian seharusnya dilempar.');
        } catch (\RuntimeException) {
            // diharapkan
        }

        $this->assertSame([], Storage::disk('local')->allFiles());
        $this->assertDatabaseCount('kuitansi', 0);
    }

    // ---------------------------------------------------------------- duplikat

    private function arsipAda(User $owner, array $override = []): Kuitansi
    {
        return Kuitansi::create(array_merge([
            'user_id' => $owner->id, 'nomor_kuitansi' => 'KWT-261030', 'nama_pihak' => 'Warung Baru', 'jenis' => 'piutang',
            'nominal_transaksi' => 1000000, 'status_pembayaran' => 'belum_lunas', 'tanggal_kuitansi' => '2026-10-30',
            'tanggal_jatuh_tempo' => '2026-11-20',
        ], $override));
    }

    public function test_similar_archive_is_warned_about_and_not_saved_until_confirmed(): void
    {
        $owner = User::factory()->create();
        $this->arsipAda($owner);

        $this->simpan($owner)->assertSessionHasErrors('duplikat');
        $this->assertDatabaseCount('kuitansi', 1);
        $this->assertSame([], Storage::disk('local')->allFiles());

        // Setelah owner memeriksa dan memastikan bukan duplikat, arsip tersimpan dengan nomor baru.
        $this->simpan($owner, ['konfirmasi_duplikat' => '1'])->assertSessionDoesntHaveErrors();
        $this->assertDatabaseCount('kuitansi', 2);
        $this->assertSame('KWT-261030-2', Kuitansi::orderByDesc('id')->firstOrFail()->nomor_kuitansi);
    }

    public function test_duplicate_check_endpoint_lists_similar_archives(): void
    {
        $owner = User::factory()->create();
        $ada = $this->arsipAda($owner);
        $kirim = fn (array $d) => $this->actingAs($owner)->postJson('/kuitansi/cek-duplikat', array_merge([
            'nama_pihak' => 'warung baru ', 'tanggal_kuitansi' => '2026-10-30', 'nominal_transaksi' => 'Rp 1.000.000',
        ], $d));

        $kirim([])->assertOk()->assertJsonCount(1, 'duplikat')->assertJsonPath('duplikat.0.nomor', 'KWT-261030')
            ->assertJsonPath('duplikat.0.nominal', 'Rp1.000.000')
            ->assertJsonPath('duplikat.0.url', route('arsip-kuitansi.show', $ada));

        $kirim(['nominal_transaksi' => '1.000.001'])->assertJsonCount(0, 'duplikat');
        $kirim(['tanggal_kuitansi' => '2026-10-29'])->assertJsonCount(0, 'duplikat');
        $kirim(['nama_pihak' => 'Toko Lain'])->assertJsonCount(0, 'duplikat');
    }

    public function test_same_physical_number_for_the_same_party_counts_as_similar(): void
    {
        $owner = User::factory()->create();
        $this->arsipAda($owner, ['nomor_kuitansi_fisik' => 'INV-77', 'tanggal_kuitansi' => '2026-09-01', 'nominal_transaksi' => 5]);

        $this->actingAs($owner)->postJson('/kuitansi/cek-duplikat', [
            'nama_pihak' => 'Warung Baru', 'tanggal_kuitansi' => '2026-10-30', 'nominal_transaksi' => '1000000', 'nomor_kuitansi_fisik' => 'inv-77',
        ])->assertJsonCount(1, 'duplikat');

        $this->actingAs($owner)->postJson('/kuitansi/cek-duplikat', [
            'nama_pihak' => 'Pihak Lain', 'tanggal_kuitansi' => '2026-10-30', 'nominal_transaksi' => '1000000', 'nomor_kuitansi_fisik' => 'INV-77',
        ])->assertJsonCount(0, 'duplikat');
    }

    public function test_other_owners_archives_never_count_as_similar(): void
    {
        $lain = User::factory()->create();
        $this->arsipAda($lain);

        $this->actingAs(User::factory()->create())->postJson('/kuitansi/cek-duplikat', [
            'nama_pihak' => 'Warung Baru', 'tanggal_kuitansi' => '2026-10-30', 'nominal_transaksi' => '1000000',
        ])->assertJsonCount(0, 'duplikat');
    }

    public function test_guest_cannot_use_the_helper_endpoints(): void
    {
        $this->postJson('/kuitansi/cek-duplikat', [])->assertUnauthorized();
        $this->getJson('/kuitansi/nomor-sementara?tanggal=2026-10-30')->assertUnauthorized();
    }
}
