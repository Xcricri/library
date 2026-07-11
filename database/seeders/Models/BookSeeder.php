<?php

namespace Database\Seeders\Models;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
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
         * artisan seed:generate --model-mode --models=Book
         *
         */


        $newData0 = \App\Models\Book::create([
            'author' => 'Penulis 1',
            'cover' => '',
            'created_at' => now(),
            'deleted_at' => NULL,
            'description' => 'Hello world!',
            'id' => 1,
            'isbn' => 'ISBN123456789',
            'published_at' => '2026-07-07',
            'publisher_name' => 'Penerbit 1',
            'slug' => 'testing-book-1',
            'stock' => 10,
            'title' => 'Testing Book 1',
            'updated_at' => now(),
        ]);
        $newData1 = \App\Models\Book::create([
            'author' => 'Penulis 2',
            'cover' => '',
            'created_at' => now(),
            'deleted_at' => NULL,
            'description' => 'Hello world',
            'id' => 2,
            'isbn' => 'ISBN123456788',
            'published_at' => '2026-07-08',
            'publisher_name' => 'Penerbit 2',
            'slug' => 'testing-book-2',
            'stock' => 10,
            'title' => 'Testing Book 2',
            'updated_at' => now(),
        ]);
    }
}
