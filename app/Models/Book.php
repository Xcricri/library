<?php

namespace App\Models;

use App\Models\Genre;
use App\Models\Loan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'slug',
        'title',
        'author',
        'publisher_name',
        'cover',
        'ebook_file',
        'isbn',
        'description',
        'published_at',
    ];

    /**
     * Summary of dates
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * Summary of users
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Book>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of genres
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Genre, Book>
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * Summary of loans
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Loan, Book, TPivotModel>
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Summary of reviews
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Review, Book>
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Summary of categories
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Category, Book,TPivotModel>
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
