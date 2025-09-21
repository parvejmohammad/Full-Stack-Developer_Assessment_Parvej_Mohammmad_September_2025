<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManagerUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'parvej@example.com'],
            [
                'name' => 'parvej',
                'password' => Hash::make('root123'),
            ]
        );
    }
}
