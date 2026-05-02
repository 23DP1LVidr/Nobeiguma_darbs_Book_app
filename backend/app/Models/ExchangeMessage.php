<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeMessage extends Model
{
    protected $fillable = [
        'exchange_id',
        'user_id',
        'message',
    ];

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}