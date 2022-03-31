<?php

namespace App\Commands;

use App\Models\Event;
use App\Services\Enums\EventStatus;
use App\Services\Enums\UserStatus;

class Cancel extends BaseCommand
{

    function processCommand($param = null)
    {
        switch ($this->user->status) {
            case UserStatus::ASK_TITLE:
            case UserStatus::ASK_ADMIN:
                $this->triggerCommand(MainMenu::class);
                break;
            case UserStatus::ASK_DESCRIPTION:
                Event::where('created_by', $this->user->id)->where('status', EventStatus::DRAFT)->delete();
                $this->triggerCommand(MainMenu::class);
                break;
            case UserStatus::EDIT_TITLE:
            case UserStatus::EDIT_DESCRIPTION:
                $this->user->editingEvent()->update([
                    'status' => EventStatus::DONE,
                ]);
                $this->triggerCommand(MainMenu::class);
                break;
        }
    }

}
