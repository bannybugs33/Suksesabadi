<?php

namespace Tests\Feature;

use App\Models\Receipt;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengingatJatuhTempoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Tidak butuh hasil build Vite saat test.
        $this->withoutVite();
    }

    public function test_login_page_shows_reminder_summary_from_database(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Rp24,45 jt');
        $response->assertSee('4 kuitansi perlu perhatian');
        $response->assertSee('Rp20.850.000 belum diselesaikan');
        $response->assertDontSee('Toko Makmur Jaya');
    }

    public function test_login_page_shows_safe_state_when_database_is_empty(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Tidak ada kuitansi yang perlu perhatian');
    }

    public function test_login_form_fields_are_unchanged(): void
    {
        $response = $this->get('/login');

        $response->assertSee('name="username"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('Masuk ke BukuTempo');
    }

    public function test_dashboard_popup_shows_nearest_upcoming_reminder(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::where('username', 'andi.owner')->firstOrFail();

        $response = $this->actingAs($owner)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Detail pengingat');
        $response->assertSee('Jatuh tempo besok');
        $response->assertSee('Kantin Melati');
    }

    public function test_dashboard_popup_falls_back_to_most_overdue_receipt(): void
    {
        $this->seed(DatabaseSeeder::class);
        $owner = User::where('username', 'andi.owner')->firstOrFail();

        // Tidak ada lagi yang akan jatuh tempo dalam 3 hari -> tampilkan yang paling lama terlambat.
        Receipt::where('code', 'KWT-240905')->update(['status' => 'lunas']);

        $response = $this->actingAs($owner)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Detail pengingat');
        $response->assertSee('Terlambat 7 hari');
        $response->assertSee('Toko Makmur Jaya');
    }

    public function test_dashboard_has_no_popup_without_reminders(): void
    {
        $owner = User::create([
            'name' => 'Budi Santoso',
            'username' => 'budi',
            'role' => 'owner',
            'store_name' => 'Toko Budi',
            'password' => 'password',
        ]);

        $response = $this->actingAs($owner)->get('/dashboard');

        $response->assertOk();
        $response->assertDontSee('Detail pengingat');
    }
}
