<?php

namespace App\Models;

use App\Models\Lunch;
use App\Models\Snack;
use App\Models\Dinner;
use App\Models\Target;
use App\Models\Breakfast;
use App\Models\DailySport;
use App\Models\PersonalDetail;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
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
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function personalDetail()
    {
        return $this->hasOne(PersonalDetail::class);
    }

    public function target()
    {
        return $this->hasOne(Target::class);
    }

    public function breakfasts()
    {
        return $this->hasMany(Breakfast::class);
    }

    public function lunches()
    {
        return $this->hasMany(Lunch::class);
    }

    public function dinners()
    {
        return $this->hasMany(Dinner::class);
    }

    public function snacks()
    {
        return $this->hasMany(Snack::class);
    }

    public function dailySport()
    {
        return $this->hasMany(DailySport::class);
    }
}

