<?php

namespace App\Commands\Edit;

use App\Commands\BaseCommand;
use App\Commands\EventInfo;
use App\Models\Event;
use App\Services\Enums\EventStatus;
use App\Services\Enums\UserStatus;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class Description extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->user->status == UserStatus::EDIT_DESCRIPTION) {
            $this->user->editingEvent()->update([
                'description' => $this->update->getMessage()->getText(),
            ]);

            $this->triggerCommand(EventInfo::class, $this->user->editingEvent()->id);

            $this->user->editingEvent()->update([
                'status' => EventStatus::DONE,
            ]);

            $this->user->status = UserStatus::NEW;
            $this->user->save();
        } else {
            $this->user->status = UserStatus::EDIT_DESCRIPTION;
            $this->user->save();

            $eventId = json_decode($this->update->getCallbackQuery()->getData(), true)['id'];
            Event::find($eventId)->update([
                'status' => EventStatus::EDITING,
            ]);

            $this->getBot()->deleteMessage(
                $this->user->chat_id,
                $this->update->getCallbackQuery()->getMessage()->getMessageId()
            );

            $this->getBot()->sendMessageWithKeyboard(
                $this->user->chat_id,
                $this->text['write_event_description'],
                new ReplyKeyboardMarkup(
                    [[$this->text['cancel']]],
                    false, true,
                )
            );
        }
    }

}
