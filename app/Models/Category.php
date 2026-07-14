<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Summary of books
     *
     * @return HasMany<Book, Category, >
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
