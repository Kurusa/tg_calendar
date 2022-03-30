<?php

namespace App\Commands;

use App\Models\Event;
use App\Utils\Twig;
use Carbon\Carbon;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class EventInfo extends BaseCommand
{

    function processCommand($param = null)
    {
        $eventId = $param ?: json_decode($this->update->getCallbackQuery()->getData(), true)['id'];
        $event = Event::find($eventId);

        $keyboard[] = [
            [
                'text'          => $this->text['edit_title'],
                'callback_data' => json_encode([
                    'a'  => 'edit_title',
                    'id' => $eventId,
                ]),
            ], [
                'text'          => $this->text['edit_description'],
                'callback_data' => json_encode([
                    'a'  => 'edit_description',
                    'id' => $eventId,
                ]),
            ],
        ];

        $keyboard[] = [
            [
                'text'          => $this->text['back'],
                'callback_data' => json_encode([
                    'a'    => 'back_to_schedule',
                    'date' => $event->eventDate->date,
                ]),
            ], [
                'text'          => $this->text['delete'],
                'callback_data' => json_encode([
                    'a'  => 'delete_event',
                    'id' => $eventId,
                ]),
            ],
        ];

        $dateObject = Carbon::createFromTimestamp($event->eventDate->date);
        $template = Twig::getInstance()->load('one_event.twig')->render([
            'event' => $event,
            'day'   => $dateObject->format('d'),
            'month' => $this->text['months'][date('n')],
            'time'  => $dateObject->format('h:i'),
        ]);
        if ($param) {
            $this->getBot()->sendMessageWithKeyboard(
                $this->user->chat_id,
                $template,
                new InlineKeyboardMarkup($keyboard)
            );
        } else {
            $this->getBot()->editMessageText(
                $this->user->chat_id,
                $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                $template,
                'HTML', false,
                new InlineKeyboardMarkup($keyboard)
            );
        }
    }

}
