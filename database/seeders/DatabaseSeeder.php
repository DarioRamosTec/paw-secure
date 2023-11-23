<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'last_name' => 'Test User',
            'email' => 'test@example.com',
            'is_active' => true,
            'password' => Hash::make('1234567890'),
            'role_id' => 1,
        ]);
    }
}
