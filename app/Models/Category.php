<?php

namespace App\Models;

use App\Models\Book;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Summary of books
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Book, Category, TPivotModel>
     */
    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
