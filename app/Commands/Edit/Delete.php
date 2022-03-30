<?php

namespace App\Commands\Edit;

use App\Commands\BaseCommand;
use App\Commands\MainMenu;
use App\Models\Event;

class Delete extends BaseCommand
{

    function processCommand($param = null)
    {
        $eventId = json_decode($this->update->getCallbackQuery()->getData(), true)['id'];
        Event::destroy($eventId);

        $this->triggerCommand(MainMenu::class);
    }

}
