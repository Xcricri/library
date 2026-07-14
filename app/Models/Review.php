<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'comment',
    ];

    /**
     * Summary of user
     *
     * @return BelongsTo<User, Review>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of book
     *
     * @return BelongsTo<Book, Review>
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
