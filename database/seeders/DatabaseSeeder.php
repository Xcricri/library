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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test admin',
            'email' => 'test@example.com',
            'role' => 'admin',
            'password' => Hash::make('password')
        ]);


        User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'role' => 'user',
            'password' => Hash::make('password')
        ]);
        $this->call(\Database\Seeders\Models\CategorySeeder::class);
        $this->call(\Database\Seeders\Models\BookSeeder::class);
    }
}
