<?php

namespace Database\Seeders\Models;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
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
         * artisan seed:generate --model-mode --models=Genre
         *
         */

        $newData0 = \App\Models\Genre::create([
            'id' => 1,
            'name' => 'Genre 1',
        ]);
        $newData1 = \App\Models\Genre::create([
            'id' => 2,
            'name' => 'Genre 2',
        ]);
    }
}
