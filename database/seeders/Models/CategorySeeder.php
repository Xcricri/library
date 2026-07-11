<?php

namespace Database\Seeders\Models;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Command :
         * artisan seed:generate --model-mode --models=Category
         *
         */


        $newData0 = \App\Models\Category::create([
            'created_at' => now(),
            'description' => 'Hello world',
            'id' => 1,
            'name' => 'Category 1',
            'updated_at' => now(),
        ]);
        $newData1 = \App\Models\Category::create([
            'created_at' => now(),
            'description' => 'Hello world',
            'id' => 2,
            'name' => 'Category 2',
            'updated_at' => now(),
        ]);
    }
}
