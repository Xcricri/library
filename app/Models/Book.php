<?php

namespace App\Models;

use App\Models\BorrowingBook;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
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
        'isbn',
        'stock',
        'description',
        'published_at',
        'category_id'
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
     * Summary of borrowings
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<BorrowingBook, Book, TPivotModel>
     */
    public function borrowings()
    {
        return $this->hasMany(BorrowingBook::class);
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Category, Book>
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Summary of wishlist
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Wishlist, Book>
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
