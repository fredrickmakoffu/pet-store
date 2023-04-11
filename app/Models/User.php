<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'avatar',
        'phone_number',
        'is_marketing',
        'is_admin',
        'last_login_at',
        'password',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_marketing' => 'boolean',
        'is_admin' => 'boolean',
    ];

    public function scopeFilter($query, $filters) 
    {
        $is_admin = isset($filters['show_admin']) && $filters['show_admin'] == "true" 
            ? [0, 1] 
            : [0];

        return $query->whereIn('is_admin', $is_admin)
            ->select('id', 'first_name', 'last_name', 'uuid', 'email', 'phone_number', 'is_admin', 'is_marketing', 'created_at');
    }
}
