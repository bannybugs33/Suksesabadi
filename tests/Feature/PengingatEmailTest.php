<?php

namespace Tests\Feature;

use App\Mail\PengingatJatuhTempoMail;
use App\Models\Kuitansi;
use App\Models\Pembayaran;
use App\Models\PengingatEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PengingatEmailTest extends TestCase
{
    use RefreshDatabase;

    private const HARI_INI = '2026-11-06';

    /**
     * @param  array<string, mixed>  $tambahan
     */
    private function buatKuitansi(User $owner, string $nomor, string $tempo, array $tambahan = []): Kuitansi
    {
        return Kuitansi::create(array_merge([
            'user_id' => $owner->id,
            'nomor_kuitansi' => $nomor,
            'nama_pihak' => 'Pihak '.$nomor,
            'jenis' => 'piutang',
            'nominal_transaksi' => 1000000,
            'status_pembayaran' => 'belum_lunas',
            'tanggal_kuitansi' => '2026-10-01',
            'tanggal_jatuh_tempo' => $tempo,
        ], $tambahan));
    }

    private function kirim(array $opsi = []): void
    {
        $this->artisan('pengingat:kirim', array_merge(['--tanggal' => self::HARI_INI], $opsi))->assertSuccessful();
    }

    public function test_sends_one_digest_for_hari_h_and_h7_only(): void
    {
        Mail::fake();
        $owner = User::factory()->create(['email' => 'owner@example.com']);

        $this->buatKuitansi($owner, 'KWT-A', '2026-11-06');   // hari H
        $this->buatKuitansi($owner, 'KWT-B', '2026-11-13');   // H-7
        $this->buatKuitansi($owner, 'KWT-C', '2026-11-10');   // bukan H maupun H-7

        $this->kirim();

        Mail::assertSentCount(1);
        Mail::assertSent(PengingatJatuhTempoMail::class, function (PengingatJatuhTempoMail $mail): bool {
            return $mail->hasTo('owner@example.com')
                && count($mail->hariH) === 1
                && count($mail->tujuhHari) === 1
                && $mail->hariH[0]['nomor'] === 'KWT-A'
                && $mail->tujuhHari[0]['nomor'] === 'KWT-B';
        });
        $this->assertSame(2, PengingatEmail::count());
    }

    public function test_skips_paid_cancelled_and_counts_only_active_payments(): void
    {
        Mail::fake();
        $owner = User::factory()->create(['email' => 'owner@example.com']);

        $lunas = $this->buatKuitansi($owner, 'KWT-LUNAS', '2026-11-06', ['nominal_transaksi' => 500000]);
        Pembayaran::create(['kuitansi_id' => $lunas->id, 'nominal' => 500000, 'tanggal_pembayaran' => '2026-10-02']);

        $this->buatKuitansi($owner, 'KWT-BATAL', '2026-11-06', ['dibatalkan_pada' => now()]);

        // Pembayarannya dibatalkan, jadi tagihan masih bersisa dan tetap diingatkan.
        $masihBersisa = $this->buatKuitansi($owner, 'KWT-SISA', '2026-11-06', ['nominal_transaksi' => 500000]);
        Pembayaran::create([
            'kuitansi_id' => $masihBersisa->id,
            'nominal' => 500000,
            'tanggal_pembayaran' => '2026-10-02',
            'status' => 'dibatalkan',
        ]);

        $this->kirim();

        Mail::assertSentCount(1);
        Mail::assertSent(PengingatJatuhTempoMail::class, function (PengingatJatuhTempoMail $mail): bool {
            return count($mail->hariH) === 1 && $mail->hariH[0]['nomor'] === 'KWT-SISA';
        });
    }

    public function test_each_reminder_is_sent_only_once(): void
    {
        Mail::fake();
        $owner = User::factory()->create();
        $this->buatKuitansi($owner, 'KWT-A', '2026-11-06');

        $this->kirim();
        $this->kirim();

        Mail::assertSentCount(1);
        $this->assertSame(1, PengingatEmail::count());
    }

    public function test_dry_run_sends_and_records_nothing(): void
    {
        Mail::fake();
        $owner = User::factory()->create();
        $this->buatKuitansi($owner, 'KWT-A', '2026-11-06');

        $this->kirim(['--dry-run' => true]);

        Mail::assertNothingSent();
        $this->assertSame(0, PengingatEmail::count());
    }

    public function test_each_owner_gets_their_own_email_and_override_address_is_used(): void
    {
        Mail::fake();
        $pertama = User::factory()->create(['email' => 'satu@example.com']);
        $kedua = User::factory()->create(['email' => 'dua@example.com']);
        $this->buatKuitansi($pertama, 'KWT-A', '2026-11-06');
        $this->buatKuitansi($kedua, 'KWT-B', '2026-11-06');

        $this->kirim();

        Mail::assertSentCount(2);
        Mail::assertSent(PengingatJatuhTempoMail::class, fn (PengingatJatuhTempoMail $mail): bool => $mail->hasTo('satu@example.com'));
        Mail::assertSent(PengingatJatuhTempoMail::class, fn (PengingatJatuhTempoMail $mail): bool => $mail->hasTo('dua@example.com'));
    }

    public function test_override_address_replaces_owner_email(): void
    {
        Mail::fake();
        config(['pengingat.email_tujuan' => 'a@example.com, b@example.com']);
        $owner = User::factory()->create(['email' => 'owner@example.com']);
        $this->buatKuitansi($owner, 'KWT-A', '2026-11-06');

        $this->kirim();

        Mail::assertSent(PengingatJatuhTempoMail::class, function (PengingatJatuhTempoMail $mail): bool {
            return $mail->hasTo('a@example.com') && $mail->hasTo('b@example.com') && ! $mail->hasTo('owner@example.com');
        });
    }
}
