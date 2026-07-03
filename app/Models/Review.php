<?php

namespace App\Models;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'comment'
    ];

    /**
     * Summary of user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Review>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of book
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Book, Review>
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
