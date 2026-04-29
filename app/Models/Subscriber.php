<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Subscriber extends Model
{
    use Notifiable;
    //
    protected $fillable = ['email', 'token', 'is_verified', 'verified_at'];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];
    protected static function boot() : void
    {
        parent::boot();

        // Generate a unique token when creating a new subscriber
        static::creating(function ($subscriber) {
            if (empty($subscriber->token)) {
                $subscriber->token = \Str::random(60);
            }
        });
    }

    //requere for sending notification to subscriber
    public function routeNotificationForMail()
    {        return $this->email;
    }

}
