<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    /**
     * Summary of fillable
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Summary of books
     *
     * @return BelongsToMany<Book, Genre>
     */
    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
