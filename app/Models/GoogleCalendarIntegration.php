<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleCalendarIntegration extends Model
{
    protected $fillable = [
        'user_id',
        'google_id',
        'email',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
