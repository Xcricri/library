<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    /**
     * Summary of fillable
     *
     * @var array
     */
    protected $fillable = ['user_id', 'book_id'];

    /**
     * Summary of user
     *
     * @return BelongsTo<User, Wishlist>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of book
     *
     * @return BelongsTo<Book, Wishlist>
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
