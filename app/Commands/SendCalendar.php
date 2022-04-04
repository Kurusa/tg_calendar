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
        $this->addMonthButton($keyboard);
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

    public function addMonthButton(array &$keyboard)
    {
        $currentMonthButton = [[
            'text'          => $this->text['months'][date('n')],
            'callback_data' => json_encode([]),
        ]];
        $nextMonthButton = [[
            'text'          => $this->text['months'][date('n', strtotime('next month'))] . ' ' . $this->text['next_month'],
            'callback_data' => json_encode([
                'a' => 'next_month',
            ]),
        ]];
        $prevMonthButton = [[
            'text'          => $this->text['prev_month'] . ' ' . $this->text['months'][date('n')],
            'callback_data' => json_encode([
                'a' => 'prev_month',
            ]),
        ]];

        if ($this->update->getCallbackQuery()) {
            $action = json_decode($this->update->getCallbackQuery()->getData(), true)['a'];
            switch ($action) {
                case 'next_month':
                    $currentMonthButton[0]['text'] = $this->text['months'][date('n', strtotime('next month'))];
                    $keyboard[] = $currentMonthButton;
                    $keyboard[] = $prevMonthButton;
                    break;
                case 'prev_month':
                case 'back_to_calendar':
                    $keyboard[] = $currentMonthButton;
                    $keyboard[] = $nextMonthButton;
                    break;
            }
        } else {
            $keyboard[] = $currentMonthButton;
            $keyboard[] = $nextMonthButton;
        }
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
        $startDate = Carbon::now();
        $action = null;
        if ($this->update->getCallbackQuery()) {
            $action = json_decode($this->update->getCallbackQuery()->getData(), true)['a'];
            if ($action == 'next_month') {
                $startDate = $startDate->addMonthsNoOverflow();
            }
        }
        $dayButtons = [];

        $n = 0;
        // first offset
        $startWeekDay = $startDate->startOfMonth()->dayOfWeek;
        for ($i = 1; $i < $startWeekDay; $i++) {
            $n++;
            $dayButtons[] = [
                'text'          => '‎',
                'callback_data' => json_encode([]),
            ];
        }

        for ($i = 1; $i <= $startDate->daysInMonth; $i++) {
            if ($n % 7 == 0) {
                $keyboard[] = $dayButtons;
                $dayButtons = [];
            }

            $n++;

            // flag if event exists
            $month = $action == 'next_month' ? date('n', strtotime('next month')) : date('n');
            $eventExists = EventDate::findEventsByDate($i, $month)->count();
            $dayButtons[] = [
                'text'          => ($eventExists ? $this->text['event_exists_emoji'] : '') . $i,
                'callback_data' => json_encode([
                    'a'     => $eventExists ? 'display_schedule' : 'create_event',
                    'date'  => Carbon::create(Carbon::now()->year, $startDate->month, $i)->timestamp,
                ]),
            ];
        }

        // last offset
        for ($i = 0; $i < 7; $i++) {
            $dayButtons[] = [
                'text'          => '‎',
                'callback_data' => json_encode([]),
            ];
            if (sizeof($dayButtons) == 7) {
                break;
            }
        }
        $keyboard[] = $dayButtons;
    }

}
