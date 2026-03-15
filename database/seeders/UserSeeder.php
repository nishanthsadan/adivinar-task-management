<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@m.com',
            'password' => Hash::make('123456'),
            'role'     => 'admin',
        ]);

        // Regular user
        User::create([
            'name'     => 'testuser01',
            'email'    => 'testuser01@m.com',
            'password' => Hash::make('123456'),
            'role'     => 'user',
        ]);

        User::create([
            'name'     => 'testuser02',
            'email'    => 'testuser02@m.com',
            'password' => Hash::make('123456'),
            'role'     => 'user',
        ]);

    }
}
