<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    /**
     * Summary of fillable
     *
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
        'category_id',
    ];

    /**
     * Summary of dates
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
    ];

    /**
     * Summary of users
     *
     * @return BelongsTo<User, Book>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of genres
     *
     * @return BelongsToMany<Genre, Book>
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * Summary of borrowings
     *
     * @return HasMany<BorrowingBook, Book, TPivotModel>
     */
    public function borrowings()
    {
        return $this->hasMany(BorrowingBook::class);
    }

    /**
     * Summary of reviews
     *
     * @return HasMany<Review, Book>
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Summary of categories
     *
     * @return BelongsTo<Category, Book>
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Summary of wishlist
     *
     * @return HasMany<Wishlist, Book>
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
