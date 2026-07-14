<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookBorrowing extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'returned_at',
        'borrowed_at',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'borrowed_at' => 'datetime',
            'due_date' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    /**
     * Summary of book
     *
     * @return BelongsTo<Book, BookBorrowing>
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Summary of user
     *
     * @return BelongsTo<User, BookBorrowing>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
