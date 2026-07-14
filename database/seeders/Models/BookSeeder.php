<?php

namespace Database\Seeders\Models;

use App\Models\Book;
use App\Models\Genre;
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


        $newData0 = Book::create([
            'author' => 'Penulis 1',
            'cover' => '',
            'deleted_at' => NULL,
            'description' => 'Hello world!',
            'id' => 1,
            'isbn' => 'ISBN123456789',
            'published_at' => '2026-07-07',
            'publisher_name' => 'Penerbit 1',
            'slug' => 'testing-book-1',
            'stock' => 10,
            'title' => 'Testing Book 1',
            'category_id' => 1,
        ]);
        $newData1 = Book::create([
            'author' => 'Penulis 2',
            'cover' => '',
            'deleted_at' => NULL,
            'description' => 'Hello world',
            'id' => 2,
            'isbn' => 'ISBN123456788',
            'published_at' => '2026-07-08',
            'publisher_name' => 'Penerbit 2',
            'slug' => 'testing-book-2',
            'stock' => 10,
            'title' => 'Testing Book 2',
            'category_id' => 2,
        ]);

        $newData0->genres()->sync([Genre::where('name', 'Genre 1')->first()->id]);
        $newData1->genres()->sync([Genre::where('name', 'Genre 2')->first()->id]);
    }
}
