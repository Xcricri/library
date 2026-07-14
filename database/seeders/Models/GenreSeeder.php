<?php

namespace Database\Seeders\Models;

use App\Models\Genre;
use Illuminate\Database\Seeder;

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
         */
        $newData0 = Genre::create([
            'id' => 1,
            'name' => 'Genre 1',
        ]);
        $newData1 = Genre::create([
            'id' => 2,
            'name' => 'Genre 2',
        ]);
    }
}
