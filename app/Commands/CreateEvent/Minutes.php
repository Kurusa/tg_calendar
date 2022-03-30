<?php

namespace App\Commands\CreateEvent;

use App\Commands\BaseCommand;
use Carbon\Carbon;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class Minutes extends BaseCommand
{

    function processCommand($param = null)
    {
        $timestamp = json_decode($this->update->getCallbackQuery()->getData(), true)['date'];
        $buttons = [];
        $keyboard = [];
        for ($i = 0; $i <= 55; $i+=5) {
            $minutes = strlen(strval($i)) == 1 ? '0' . $i : $i;
            $buttons[] = [
                'text' => Carbon::createFromTimestamp($timestamp)->hour . ':' . $minutes,
                'callback_data' => json_encode([
                    'a'    => 'event_minutes',
                    'date' => Carbon::createFromTimestamp($timestamp)->addMinutes($i)->timestamp,
                ]),
            ];

            if (sizeof($buttons) == 2) {
                $keyboard[] = $buttons;
                $buttons = [];
            }
        }

        $keyboard[] = [[
            'text'          => $this->text['back'],
            'callback_data' => json_encode([
                'a' => 'back_to_calendar',
            ]),
        ]];

        $this->getBot()->editMessageText(
            $this->user->chat_id,
            $this->update->getCallbackQuery()->getMessage()->getMessageId(),
            $this->text['select_event_minute'],
            null, false,
            new InlineKeyboardMarkup($keyboard),
        );
    }

}
