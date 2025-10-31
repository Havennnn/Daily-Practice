<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'content'
    ];

    public function user() {
        return $this->belongTo(User::class);
    }

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}
