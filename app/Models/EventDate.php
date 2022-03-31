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

    public static function findEventsByDate(int $day, int $month)
    {
        return EventDate::whereBetween('date', [
            Carbon::create(Carbon::now()->year, $month, $day)->startOfDay()->timestamp,
            Carbon::create(Carbon::now()->year, $month, $day)->endOfDay()->timestamp,
        ])->get();
    }

}
