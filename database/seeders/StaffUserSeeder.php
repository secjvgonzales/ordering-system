<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'staff@example.com',
            ],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'status' => 'active',
            ]
        );
    }
}
