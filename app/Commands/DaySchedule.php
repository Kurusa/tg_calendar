<?php

namespace App\Commands;

use App\Models\EventDate;
use Carbon\Carbon;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class DaySchedule extends BaseCommand
{

    function processCommand($param = null)
    {
        $timestamp = json_decode($this->update->getCallbackQuery()->getData(), true)['date'];
        $date = Carbon::createFromTimestamp($timestamp);
        $eventDates = EventDate::findEventsByDate($date->day, $date->month);

        $keyboard = [];
        foreach ($eventDates as $eventDate) {
            $keyboard[] = [[
                'text'          => $eventDate->event->title,
                'callback_data' => json_encode([
                    'a'  => 'event_info',
                    'id' => $eventDate->event->id,
                ]),
            ]];
        }

        $keyboard[] = [
            [
                'text'          => $this->text['create_event'],
                'callback_data' => json_encode([
                    'a'    => 'create_event_from_schedule',
                    'date' => $timestamp,
                ]),
            ], [
                'text'          => $this->text['back'],
                'callback_data' => json_encode([
                    'a' => 'back_to_calendar',
                ]),
            ],
        ];

        $dateObject = Carbon::createFromTimestamp($timestamp);
        $this->getBot()->editMessageText(
            $this->user->chat_id,
            $this->update->getCallbackQuery()->getMessage()->getMessageId(),
            $this->text['events_list'] . '<b>' . $dateObject->format('d') . ' ' . $this->text['months'][date('n')] . ' ' . $dateObject->format('h:i') . '</b>',
            'HTML', false,
            new InlineKeyboardMarkup($keyboard)
        );
    }

}
