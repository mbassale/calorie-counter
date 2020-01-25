<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'first_name', 'last_name', 'email', 'password',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Attributes appended on JSON results
     * @var array
     */
    protected $appends = ['full_name', 'caption'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function meals()
    {
        return $this->hasMany(Meal::class, 'user_id');
    }

    public function isAdmin()
    {
        return $this->role_id == Role::ADMIN;
    }

    public function isManager()
    {
        return $this->role_id == Role::MANAGER;
    }

    public function isUser()
    {
        return $this->role_id == Role::USER;
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getCaptionAttribute()
    {
        return "{$this->full_name} <{$this->email}>";
    }
}
