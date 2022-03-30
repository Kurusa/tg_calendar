<?php

namespace App\Commands;

use App\Models\EventDate;
use Carbon\Carbon;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class SendCalendar extends BaseCommand
{

    function processCommand($param = null)
    {
        $keyboard = [];
        $this->addTopButton($keyboard);
        $this->addWeekDays($keyboard);
        $this->addDayButtons($keyboard);

        if ($this->update->getCallbackQuery()) {
            $this->getBot()->editMessageText(
                $this->user->chat_id,
                $this->update->getCallbackQuery()->getMessage()->getMessageId(),
                $this->text['calendar_text'],
                null, false,
                new InlineKeyboardMarkup($keyboard),
            );
        } else {
            $this->getBot()->sendMessageWithKeyboard(
                $this->user->chat_id,
                $this->text['calendar_text'],
                new InlineKeyboardMarkup($keyboard),
            );
        }
    }

    public function addTopButton(array &$keyboard)
    {
        $keyboard[] = [
            [
                'text'          => $this->text['months'][date('n')],
                'callback_data' => json_encode([]),
            ]
        ];
    }

    public function addWeekDays(array &$keyboard)
    {
        $keyboard[] = array_map(function ($weekDay) {
            return [
                'text'          => $weekDay,
                'callback_data' => json_encode([]),
            ];
        }, $this->text['week_days']);
    }

    public function addDayButtons(array &$keyboard)
    {
        $dayButtons = [];

        // first offset
        $startWeekDay = Carbon::now()->startOfMonth()->dayOfWeek;
        for ($i = 1; $i <= $startWeekDay; $i++) {
            $dayButtons[] = [
                'text'          => '‎',
                'callback_data' => json_encode([]),
            ];
        }

        $n = 1;
        for ($i = $startWeekDay; $i <= Carbon::now()->daysInMonth; $i++) {
            if ($i % 7 == 0) {
                $keyboard[] = $dayButtons;
                $dayButtons = [];
            }

            $dayNumber = $n++;

            // flag if event exists
            $eventExists = EventDate::findEventsByDay($dayNumber)->count();
            $dayButtons[] = [
                'text'          => ($eventExists ? $this->text['event_exists_emoji'] : '') . $dayNumber,
                'callback_data' => json_encode([
                    'a'     => $eventExists ? 'display_schedule' : 'create_event',
                    'date'  => Carbon::create(Carbon::now()->year, Carbon::now()->month, $dayNumber)->timestamp,
                ]),
            ];
        }

        // last offset
        for ($i = 0; $i <= 8 - count($dayButtons); $i++) {
            $dayButtons[] = [
                'text'          => '‎',
                'callback_data' => json_encode([]),
            ];
        }
        $keyboard[] = $dayButtons;
    }

}
