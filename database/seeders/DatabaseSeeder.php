<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear o actualizar Administrador
        User::updateOrCreate(
            ['email' => 'admin@logforge.com'], // Condición de búsqueda
            [
                'name' => 'Admin Sistema',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ] // Datos a crear/actualizar
        );

        // 2. Crear o actualizar Candidato
        User::updateOrCreate(
            ['email' => 'juan@gmail.com'],
            [
                'name' => 'Juan Candidato',
                'password' => Hash::make('user123'),
                'role' => 'candidate'
            ]
        );

        // 3. Crear o actualizar Empresa
        User::updateOrCreate(
            ['email' => 'hr@techcompany.com'],
            [
                'name' => 'Tech Company',
                'password' => Hash::make('empresa123'),
                'role' => 'company'
            ]
        );
    }
}
