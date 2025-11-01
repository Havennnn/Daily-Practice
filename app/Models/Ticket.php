<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'leased_to_id',
        'title',
        'description',
        'status',
        'priority'
    ];

    public function creator() {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function lessee() {
        return $this->belongsTo(User::class, 'leased_to_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
}
