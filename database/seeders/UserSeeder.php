<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun contoh hanya dibuat pada lingkungan lokal/testing.
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $accounts = [
            [
                'email' => 'admin@hakiku.ac.id',
                'name' => 'Admin HAKIKU',
                'identity_number' => 'ADMIN001',
                'user_type' => 'admin',
                'role' => 'admin',
                'password' => config('services.hakiku_seed.admin_password'),
            ],
            [
                'email' => 'mahasiswa@hakiku.ac.id',
                'name' => 'Mahasiswa HAKIKU',
                'identity_number' => '123456789',
                'user_type' => 'mahasiswa',
                'role' => 'user',
                'password' => config('services.hakiku_seed.mahasiswa_password'),
            ],
            [
                'email' => 'dosen@hakiku.ac.id',
                'name' => 'Dosen HAKIKU',
                'identity_number' => '1987654321',
                'user_type' => 'dosen',
                'role' => 'user',
                'password' => config('services.hakiku_seed.dosen_password'),
            ],
        ];

        foreach ($accounts as $account) {
            $password = $account['password'];

            if (! is_string($password) || trim($password) === '') {
                throw new RuntimeException(
                    'Password akun contoh HAKIKU belum diatur di konfigurasi lokal.'
                );
            }

            unset($account['password']);

            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    ...$account,
                    'password' => Hash::make($password),
                ]
            );
        }
    }
}
