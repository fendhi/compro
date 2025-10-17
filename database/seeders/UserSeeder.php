<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Owner - Full Access (if not exists)
        User::firstOrCreate(
            ['username' => 'owner'],
            [
                'name' => 'Owner OrindPOS',
                'email' => 'owner@orindpos.com',
                'password' => Hash::make('owner123'),
                'role' => 'owner',
                'status' => 'active',
            ]
        );

        // Admin - Manager Level (if not exists)
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@orindpos.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Kasir 1 - Cashier Level (if not exists)
        User::firstOrCreate(
            ['username' => 'kasir1'],
            [
                'name' => 'Kasir 1',
                'email' => 'kasir1@orindpos.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'status' => 'active',
            ]
        );

        // Kasir 2 - Cashier Level (if not exists)
        User::firstOrCreate(
            ['username' => 'kasir2'],
            [
                'name' => 'Kasir 2',
                'email' => 'kasir2@orindpos.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'status' => 'active',
            ]
        );
    }
}
