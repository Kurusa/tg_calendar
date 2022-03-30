<?php

namespace App\Commands\CreateEvent;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Services\Enums\EventStatus;
use App\Services\Enums\UserStatus;
use App\Utils\Twig;
use Carbon\Carbon;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Description extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->user->status == UserStatus::ASK_DESCRIPTION) {
            $dateObject = Carbon::createFromTimestamp($this->user->draftEvent()->eventDate->date);
            $template = Twig::getInstance()->load('one_event.twig');
            $this->getBot()->sendMessage($this->user->chat_id, $this->text['event_created'] . $template->render([
                    'event' => $this->user->draftEvent(),
                    'day'   => $dateObject->format('d'),
                    'month' => $this->text['months'][date('n')],
                    'time'  => $dateObject->format('h:i'),
                ]), 'HTML',
            );

            $this->user->draftEvent()->update([
                'description' => $this->update->getMessage()->getText(),
                'status'      => EventStatus::DONE,
            ]);

            $this->triggerCommand(MainMenu::class);
        } else {
            $this->user->status = UserStatus::ASK_DESCRIPTION;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard($this->user->chat_id, $this->text['write_event_description'], new ReplyKeyboardMarkup([
                [$this->text['cancel']]
            ], false, true));
        }
    }

}
