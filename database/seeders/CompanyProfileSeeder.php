<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyProfile;
use App\Models\User;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Buscamos al usuario Zara (ID 1)
        $user = User::find(1);

        if ($user) {
            CompanyProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => 'Zara Global S.A.',
                    'website' => 'https://www.zara.com',
                    'description' => 'Empresa líder en el sector textil y moda.',
                ]
            );
        }
    }
}
