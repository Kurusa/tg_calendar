<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventDate extends Model {

    protected $table = 'event_dates';

    protected $fillable = [
        'event_id',
        'date',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public static function findEventsByDay(int $day)
    {
        return EventDate::whereBetween('date', [
            Carbon::create(Carbon::now()->year, Carbon::now()->month, $day)->startOfDay()->timestamp,
            Carbon::create(Carbon::now()->year, Carbon::now()->month, $day)->endOfDay()->timestamp,
        ])->get();
    }

}
