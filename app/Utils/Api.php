<?php

namespace App\Utils;

use TelegramBot\Api\{
    BotApi,
    Exception,
    InvalidArgumentException,
    Types\Message,
};

class Api extends BotApi
{

    public function __construct($token, $trackerToken = null)
    {
        parent::__construct($token, $trackerToken);
    }

    public function sendMessageWithKeyboard(
        int $chatId,
        string $text,
        $keyboard,
        int $reply_to_message_id = null,
    ): Message {
        try {
            return $this->sendMessage($chatId, $text, 'HTML', true, $reply_to_message_id, $keyboard);
        } catch (InvalidArgumentException | Exception $e) {
            error_log($e);
        }
    }

}
