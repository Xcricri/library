<?php

namespace Database\Seeders\Models;

use App\Models\Category;
use Illuminate\Database\Seeder;

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
         */
        $newData0 = Category::create([
            'id' => 1,
            'name' => 'Category 1',
        ]);
        $newData1 = Category::create([
            'id' => 2,
            'name' => 'Category 2',
        ]);
    }
}
