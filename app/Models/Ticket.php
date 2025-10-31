<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority'
    ];

    public function creator() {
        return $this->belongsToMany(User::class, 'creator_id');
    }

    public function lessee() {
        return $this->belongsToMany(User::class, 'leased_to_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
}
