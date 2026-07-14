<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name'];

    /**
     * Summary of users
     *
     * @return BelongsToMany<User, Role, TPivotModel>
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
