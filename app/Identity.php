<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Identity extends Model
{
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'provider_id',
        'provider',
        'avatar',
        'token',
        'refresh_token',
        'expires_at',

    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
