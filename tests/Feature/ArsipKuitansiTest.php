<?php

namespace Tests\Feature;

use App\Models\User;
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
}
