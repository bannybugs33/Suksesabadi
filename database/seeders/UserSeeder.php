<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Andi Saputra',
                'email' => 'andi@suksesabadi.com',
                'password' => Hash::make('password'),
            ],
            [
                'id' => 2,
                'name' => 'Safira Alifiya',
                'email' => 'safira@suksesabadi.com',
                'password' => Hash::make('password'),
            ],
            [
                'id' => 3,
                'name' => 'Admin Sukses Abadi',
                'email' => 'admin@suksesabadi.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::query()->updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
