<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model {

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'status',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function eventDate(): HasOne
    {
        return $this->hasOne(EventDate::class, 'event_id', 'id');
    }

}
