<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //admin user
        User::create([
        'name' => 'System Admin',
        'email' => 'admin',
        'password' => Hash::make('admin'),
        'role' => 'admin',
        'status' => 'active',
    ]);
    }
}
