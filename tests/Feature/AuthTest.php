<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('BukuTempo');
        $response->assertSee('Selamat datang kembali');
        $response->assertSee('Username');
        $response->assertSee('Password');
        $response->assertSee('Utang–piutang rapi', false);
    }

    public function test_users_can_authenticate_using_username(): void
    {
        $user = User::factory()->create([
            'email' => 'andi@suksesabadi.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'username' => 'andi.owner',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'andi@suksesabadi.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'andi@suksesabadi.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'andi@suksesabadi.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'andi@suksesabadi.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Anda telah berhasil keluar.');
    }

    public function test_unauthenticated_users_are_redirected_to_login_when_accessing_rekapitulasi(): void
    {
        $response = $this->get('/rekapitulasi');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_are_redirected_away_from_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_root_redirects_to_login_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_root_redirects_to_dashboard_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_can_authenticate_using_email_local_part_as_username(): void
    {
        // Memakai bagian sebelum '@' sebagai username; harus jalan di SQLite maupun MySQL.
        User::factory()->create([
            'email' => 'sari@suksesabadi.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'username' => 'sari',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_login_screen_shows_due_date_reminder_summary(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('PIUTANG BULAN INI');
        $response->assertSee('4 kuitansi perlu perhatian');
        $response->assertSee('Rp20.850.000 belum diselesaikan');
        $response->assertDontSee('Toko Makmur Jaya');
    }

    public function test_login_screen_shows_safe_state_when_no_reminders(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Tidak ada kuitansi yang perlu perhatian');
    }

    public function test_successful_login_flashes_reminder_summary_for_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post('/login', [
            'username' => 'andi.owner',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('pengingat_login', fn (array $ringkasan) => $ringkasan['jumlah'] === 4
            && $ringkasan['terlambat'] === 3);
    }
}
