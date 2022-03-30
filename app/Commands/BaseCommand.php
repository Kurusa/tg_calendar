<?php

namespace App\Commands;

use App\Services\Enums\UserRole;
use App\Services\Enums\UserStatus;
use TelegramBot\Api\Types\Update;
use App\Models\User;
use App\Utils\Api;

abstract class BaseCommand
{

    protected User $user;

    protected $botUser;

    protected array $text;

    protected Update $update;

    private $bot;

    public function __construct(Update $update)
    {
        $this->update = $update;
        if ($update->getCallbackQuery()) {
            $this->botUser = $update->getCallbackQuery()->getFrom();
        } elseif ($update->getMessage()) {
            $this->botUser = $update->getMessage()->getFrom();
        } elseif ($update->getInlineQuery()) {
            $this->botUser = $update->getInlineQuery()->getFrom();
        } else {
            throw new \Exception('cant get telegram user data');
        }
    }

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

        if ($this->user->isAdmin()) {
            $this->processCommand($param);
        }
    }

    public function getBot(): Api
    {
        if (!$this->bot) {
            $this->bot = new Api(env('TELEGRAM_BOT_TOKEN'));
        }

        return $this->bot;
    }

    function triggerCommand($class, $param = null)
    {
        (new $class($this->update))->handle($param);
    }

    abstract function processCommand($param = null);

}
