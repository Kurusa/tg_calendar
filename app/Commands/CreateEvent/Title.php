<?php

namespace App\Commands\CreateEvent;

use App\Commands\BaseCommand;
use App\Models\Event;
use App\Models\EventDate;
use App\Services\Enums\EventStatus;
use App\Services\Enums\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Title extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->user->status == UserStatus::ASK_TITLE) {
            $this->user->status = UserStatus::NEW;
            $this->user->save();

            $this->user->draftEvent()->update([
                'title' => $this->update->getMessage()->getText(),
            ]);
            $this->triggerCommand(Description::class);
        } else {
            Event::create([
                'created_by' => $this->user->id,
                'status'     => EventStatus::DRAFT,
            ]);
            EventDate::create([
                'event_id' => $this->user->draftEvent()->id,
                'date'     => json_decode($this->update->getCallbackQuery()->getData(), true)['date'],
            ]);

            $this->getBot()->deleteMessage(
                $this->user->chat_id,
                $this->update->getCallbackQuery()->getMessage()->getMessageId()
            );

            $this->user->status = UserStatus::ASK_TITLE;
            $this->user->save();

            $this->getBot()->sendMessageWithKeyboard(
                $this->user->chat_id,
                $this->text['write_event_title'],
                new ReplyKeyboardMarkup(
                    [[$this->text['cancel']]],
                    false, true,
                )
            );
        }
    }

}
