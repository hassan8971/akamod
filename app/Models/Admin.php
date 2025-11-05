<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// Import the base Authenticatable model
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Set the guard to 'admin' (good practice for multi-auth)
     *
     * @var string
     */
    protected $guard = 'admin';



    protected $fillable = [
      'name', 'username', 'email', 'password', 'mobile',
    ];

      /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
