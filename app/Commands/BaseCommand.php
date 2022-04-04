<?php

namespace App\Commands;

use App\Services\Enums\UserRole;
use App\Services\Enums\UserStatus;
use TelegramBot\Api\Types\Update;
use App\Models\User;

abstract class BaseCommand extends BasicMessages
{

    protected User $user;

    protected $botUser;

    protected array $text;

    protected Update $update;

    function handle($param = null)
    {
        $this->text = require(__DIR__ . '/../config/texts.php');

        $this->user = User::firstOrCreate(
            ['chat_id' => $this->botUser->getId()],
            [
                'user_name'  => $this->botUser->getUsername(),
                'first_name' => $this->botUser->getFirstName(),
                'role'       => UserRole::USER,
                'status'     => UserStatus::NEW,
            ],
        );

        $this->processCommand($param);
    }

    function triggerCommand($class, $param = null)
    {
        (new $class($this->update))->handle($param);
    }

    abstract function processCommand($param = null);

}
