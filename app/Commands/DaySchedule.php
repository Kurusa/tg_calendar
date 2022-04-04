<?php

namespace App\Commands;

use App\Models\EventDate;
use App\Utils\Twig;
use Carbon\Carbon;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class DaySchedule extends BaseCommand
{

    function processCommand($param = null)
    {
        $timestamp = json_decode($this->update->getCallbackQuery()->getData(), true)['date'];
        $date = Carbon::createFromTimestamp($timestamp);
        $eventDates = EventDate::findEventsByDate($date->day, $date->month);

        if (!$this->user->isAdmin()) {
            $template = Twig::getInstance()->load('event_list.twig')->render([
                'date'   => $date->format('d') . ' ' . $this->text['months'][$date->format('n')],
                'events' => $eventDates,
            ]);

            $this->getBot()->answerCallbackQuery(
                $this->update->getCallbackQuery()->getId(),
                $template,
                true,
            );
            return false;
        }

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

        $this->getBot()->editMessageText(
            $this->user->chat_id,
            $this->update->getCallbackQuery()->getMessage()->getMessageId(),
            $this->text['events_list'] . '<b>' . $date->format('d') . ' ' . $this->text['months'][$date->format('n')] . ' ' . $date->format('h:i') . '</b>',
            'HTML', false,
            new InlineKeyboardMarkup($keyboard)
        );
    }

}
