<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $fillable = [
        'requester_id',
        'owner_id',
        'requested_book_id',
        'offered_book_id',
        'status',
        'conversation_id',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function requestedBook()
    {
        return $this->belongsTo(Book::class, 'requested_book_id');
    }

    public function offeredBook()
    {
        return $this->belongsTo(Book::class, 'offered_book_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}