<?php

namespace App\Commands\CreateEvent;

use App\Commands\BaseCommand;
use Carbon\Carbon;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class Hour extends BaseCommand
{

    function processCommand($param = null)
    {
        if (!$this->user->isAdmin()) {
            return false;
        }

        $timestamp = json_decode($this->update->getCallbackQuery()->getData(), true)['date'];
        $buttons = [];
        $keyboard = [];
        for ($i = 7; $i <= 24; $i++) {
            $hour = strlen(strval($i)) == 1 ? '0' . $i : $i;

            $buttons[] = [
                'text' => $hour . ':00',
                'callback_data' => json_encode([
                    'a'    => 'event_hour',
                    'date' => Carbon::createFromTimestamp($timestamp)->addHours($i)->timestamp,
                ]),
            ];
            if ($i % 6 == 0) {
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
            $this->text['select_event_hour'],
            null, false,
            new InlineKeyboardMarkup($keyboard),
        );
    }

}
