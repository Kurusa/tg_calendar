<?php

namespace App\Commands;

use App\Services\Enums\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class MainMenu extends BaseCommand
{

    function processCommand($param = null)
    {
        $this->user->status = UserStatus::NEW;
        $this->user->save();

        $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['main_menu'], new ReplyKeyboardMarkup([
            [$this->text['add_admin']],
        ], false, true));

        $this->triggerCommand(SendCalendar::class);
    }

}
