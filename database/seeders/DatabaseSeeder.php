<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
// 1. Administrador
        User::create([
            'name' => 'Admin Sistema',
            'email' => 'admin@logforge.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // 2. Empresa
        User::create([
            'name' => 'Tech Company',
            'email' => 'hr@techcompany.com',
            'password' => Hash::make('empresa123'),
            'role' => 'company'
        ]);

        // 3. Candidato
        User::create([
            'name' => 'Juan',
            'email' => 'juan@gmail.com',
            'password' => Hash::make('user123'),
            'role' => 'user'
        ]);
    }
}
