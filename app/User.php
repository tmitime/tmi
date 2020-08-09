<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'email_verified_at', 
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

    public function identities()
    {
        return $this->hasMany(Identity::class);
    }


    /**
     * Search a user by the given email and identity provider information 
     * or create new if not found
     * 
     * @param string $email
     * @param string $provider
     * @param string $providerId
     * @param array $attributes
     * 
     * @return \App\User;
     */
    public static function findFromIdentityOrCreate($email, $provider, $providerId, $attributes = [])
    {
        $found = static::where('email', $email)->whereHas('identities', function (Builder $query) use($provider, $providerId) {
            $query->where('provider', $provider)->where('provider_id', $providerId);
        })->first();

        return $found ?? User::create([
            'name' => $attributes['name'] ?? $email,
            'email' => $email,
            'password' => $attributes['password'] ?? Hash::make(Str::random(20)),
            'email_verified_at' => $attributes['email_verified_at'] ?? null,
        ]);
    }

}
