<?php

namespace Tests\Feature;

use App\Models\Kuitansi;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Hanya owner yang sudah masuk yang boleh mengakses halaman dan foto arsip.
 */
class ProteksiAksesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: string}>
     */
    public static function halamanTerlindungi(): array
    {
        return [
            'dashboard' => ['/dashboard'],
            'jatuh tempo' => ['/jatuh-tempo'],
            'arsip kuitansi' => ['/arsip-kuitansi'],
            'detail arsip' => ['/arsip-kuitansi/1'],
            'foto arsip' => ['/arsip-kuitansi/1/bukti'],
            'kuitansi baru' => ['/kuitansi/baru'],
            'rekapitulasi' => ['/rekapitulasi'],
        ];
    }

    #[DataProvider('halamanTerlindungi')]
    public function test_guest_is_redirected_to_login(string $url): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get($url)->assertRedirect(route('login'));
    }

    public function test_guest_cannot_submit_protected_actions(): void
    {
        $this->post('/kuitansi/baru', [])->assertRedirect(route('login'));
        $this->post('/jatuh-tempo/1/pembayaran', [])->assertRedirect(route('login'));
        $this->post('/logout')->assertRedirect(route('login'));
    }

    public function test_private_pages_are_not_cacheable_by_the_browser(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_owner_can_open_own_receipt_photo(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::query()->findOrFail(1);
        $kuitansi = Kuitansi::where('nama_pihak', 'Warung Bu Sari')->firstOrFail();

        $response = $this->actingAs($owner)->get(route('arsip-kuitansi.bukti', $kuitansi));

        $response->assertOk();
        $this->assertStringContainsString('svg', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_other_user_cannot_open_someone_elses_receipt_photo(): void
    {
        $this->seed(DatabaseSeeder::class);
        $kuitansi = Kuitansi::where('nama_pihak', 'Warung Bu Sari')->firstOrFail();
        $orangLain = User::factory()->create();

        $this->actingAs($orangLain)->get(route('arsip-kuitansi.bukti', $kuitansi))->assertForbidden();
    }

    public function test_receipt_without_photo_returns_not_found(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::query()->findOrFail(1);
        $kuitansi = Kuitansi::where('nama_pihak', 'Kantin Melati')->firstOrFail();

        $this->actingAs($owner)->get(route('arsip-kuitansi.bukti', $kuitansi))->assertNotFound();
    }

    public function test_uploaded_photo_is_private_and_only_served_to_logged_in_owner(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $owner = User::factory()->create();

        $this->actingAs($owner)->post('/kuitansi/baru', [
            'tanggal_kuitansi' => '2026-09-29',
            'nama_pihak' => 'Toko Berkah Abadi',
            'jenis' => 'piutang',
            'nominal_transaksi' => '150000',
            'tanggal_jatuh_tempo' => '2026-10-10',
            'bukti' => UploadedFile::fake()->create('struk.jpg', 100, 'image/jpeg'),
        ])->assertRedirect();

        $kuitansi = Kuitansi::where('nama_pihak', 'Toko Berkah Abadi')->firstOrFail();

        // Tidak ada salinan di folder publik.
        Storage::disk('public')->assertMissing($kuitansi->bukti_kuitansi);
        $this->assertStringStartsNotWith('storage/', $kuitansi->bukti_kuitansi);

        // Halaman detail memakai URL terlindungi, bukan /storage/...
        $this->actingAs($owner)->get(route('arsip-kuitansi.show', $kuitansi))
            ->assertOk()
            ->assertSee("/arsip-kuitansi/{$kuitansi->id}/bukti")
            ->assertDontSee('/storage/kuitansi');

        $this->actingAs($owner)->get(route('arsip-kuitansi.bukti', $kuitansi))->assertOk();

        // Setelah keluar, foto yang sama tidak bisa dibuka.
        $this->post('/logout');
        $this->get(route('arsip-kuitansi.bukti', $kuitansi))->assertRedirect(route('login'));
    }

    public function test_legacy_photo_in_public_folder_is_still_served_only_through_protected_route(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Storage::disk('public')->put('kuitansi/lama.jpg', 'isi-foto-lama');

        $owner = User::factory()->create();
        $kuitansi = $this->buatKuitansi($owner, 'storage/kuitansi/lama.jpg');

        $this->actingAs($owner)->get(route('arsip-kuitansi.show', $kuitansi))
            ->assertOk()
            ->assertDontSee('/storage/kuitansi');

        $this->actingAs($owner)->get(route('arsip-kuitansi.bukti', $kuitansi))->assertOk();
    }

    public function test_command_moves_legacy_photos_from_public_to_private_storage(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Storage::disk('public')->put('kuitansi/lama.jpg', 'isi-foto-lama');

        $owner = User::factory()->create();
        $kuitansi = $this->buatKuitansi($owner, 'storage/kuitansi/lama.jpg');

        $this->artisan('bukti:amankan')->assertSuccessful();

        Storage::disk('public')->assertMissing('kuitansi/lama.jpg');
        Storage::disk('local')->assertExists('kuitansi/lama.jpg');
        $this->assertSame('kuitansi/lama.jpg', $kuitansi->fresh()->bukti_kuitansi);

        $this->actingAs($owner)->get(route('arsip-kuitansi.bukti', $kuitansi))->assertOk();
    }

    public function test_path_traversal_in_stored_value_is_never_served(): void
    {
        $owner = User::factory()->create();
        $kuitansi = $this->buatKuitansi($owner, '../../.env');

        $this->actingAs($owner)->get(route('arsip-kuitansi.bukti', $kuitansi))->assertNotFound();
    }

    public function test_demo_receipt_image_is_no_longer_publicly_reachable(): void
    {
        $this->assertFileDoesNotExist(public_path('images/kuitansi-warung-bu-sari.svg'));
    }

    private function buatKuitansi(User $owner, ?string $bukti): Kuitansi
    {
        return Kuitansi::create([
            'user_id' => $owner->id,
            'nomor_kuitansi' => 'KWT-TEST-'.uniqid(),
            'nama_pihak' => 'Pihak Uji',
            'role_pihak' => 'Pelanggan',
            'jenis' => 'piutang',
            'nominal_transaksi' => 100000,
            'status_pembayaran' => 'belum_lunas',
            'tanggal_kuitansi' => '2026-09-29',
            'tanggal_jatuh_tempo' => '2026-10-10',
            'bukti_kuitansi' => $bukti,
        ]);
    }
}
