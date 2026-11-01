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

    public function test_after_logout_owner_can_no_longer_open_protected_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk();

        $this->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect(route('login'));
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

    public function test_login_screen_only_shows_the_login_form_without_store_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Selamat datang kembali');
        $response->assertDontSee('PIUTANG');
        $response->assertDontSee('perlu perhatian');
        $response->assertDontSee('belum diselesaikan');
        $response->assertDontSee('Rp');
        $response->assertDontSee('Toko Makmur Jaya');
        $response->assertDontSee('Warung Bu Sari');
        $response->assertDontSee('Kantin Melati');
    }

    public function test_login_screen_does_not_prefill_credentials(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertDontSee('andi.owner');
        $response->assertDontSee('value="password"', false);
    }

    public function test_wrong_credentials_show_a_message_asking_owner_to_fix_the_input(): void
    {
        User::factory()->create([
            'email' => 'andi@suksesabadi.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->followingRedirects()->from('/login')->post('/login', [
            'username' => 'andi.owner',
            'password' => 'salah-total',
        ]);

        $this->assertGuest();
        $response->assertSee('Periksa kembali isian Anda');
        $response->assertSee('Selamat datang kembali');
    }

    public function test_empty_password_shows_indonesian_message(): void
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'andi.owner',
            'password' => '',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['password' => 'Password wajib diisi.']);
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        User::factory()->create([
            'email' => 'andi@suksesabadi.com',
            'password' => Hash::make('password'),
        ]);

        foreach (range(1, 5) as $i) {
            $this->from('/login')->post('/login', ['username' => 'andi.owner', 'password' => 'salah']);
        }

        // Percobaan ke-6 diblokir walaupun password-nya benar.
        $response = $this->from('/login')->post('/login', [
            'username' => 'andi.owner',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan', session('errors')->first('email'));
    }

    public function test_login_always_lands_on_dashboard_even_after_opening_a_protected_url(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/arsip-kuitansi/3/bukti')->assertRedirect(route('login'));

        $this->post('/login', [
            'username' => 'andi.owner',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
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
