<?php

namespace App\Commands;

use App\Models\User;
use App\Services\Enums\UserRole;
use App\Services\Enums\UserStatus;

class AddAdmin extends BaseCommand
{

    function processCommand($param = null)
    {
        if ($this->user->status == UserStatus::ASK_ADMIN) {
            $user = User::where('user_name', ltrim($this->update->getMessage()->getText(), '@'))->first();
            if ($user) {
                $user->update([
                    'role' => UserRole::ADMIN,
                ]);
                $this->getBot()->sendMessage(
                    $this->user->chat_id,
                    $this->text['updated'],
                );
                $this->triggerCommand(MainMenu::class);
            } else {
                $this->getBot()->sendMessage(
                    $this->user->chat_id,
                    $this->text['cant_find_user'],
                );
            }
        } else {
            $this->user->update([
                'status' => UserStatus::ASK_ADMIN,
            ]);

            $this->sendMessageWithBackButton($this->text['write_admin_username']);
        }
    }

}
