<?php

namespace App\Entity;

use App\Http\Transformers\UserTransformer;
use App\Supports\Shared\HasTransformer;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasTransformer, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'activated', 'first_time_login', 'email', 'password', 'avatar', 'mobile',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @var array
     */
    protected $casts = [

        'activated' => 'boolean',
    ];

    /**
     * @var string Default transformer class
     */
    protected $transformer = UserTransformer::class;
}
