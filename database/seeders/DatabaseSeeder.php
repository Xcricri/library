<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\Models\BookSeeder;
use Database\Seeders\Models\CategorySeeder;
use Database\Seeders\Models\GenreSeeder;
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
        $this->call(RoleSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Test admin',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $staf = User::factory()->create([
            'name' => 'Test Staff',
            'email' => 'teststaff@example.com',
            'password' => Hash::make('password'),
        ]);

        $member = User::factory()->create([
            'name' => 'Test Member',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->roles()->sync([Role::where('name', 'admin')->first()->id]);
        $staf->roles()->sync([Role::where('name', 'staff')->first()->id]);
        $member->roles()->sync([Role::where('name', 'member')->first()->id]);

        $this->call(CategorySeeder::class);
        $this->call(GenreSeeder::class);
        $this->call(BookSeeder::class);
    }
}
