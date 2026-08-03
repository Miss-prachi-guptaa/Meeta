<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@meeta.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '9999999999',
                'password' => Hash::make('Admin@123'),
                'status' => 'active',
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}