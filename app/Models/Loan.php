<?php

namespace App\Models;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'borrowed_at',
        'loan_date',
        'due_date',
        'return_date'
    ];

    /**
     * Summary of book
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Book, Loan>
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Summary of user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Loan>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
