<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    /**
     * Summary of users
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, Role, TPivotModel>
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
