<?php

namespace App;

use App\Commands\InlineQueryHandler;
use App\Commands\MainMenu;
use App\Models\User;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Update;

class WebhookController
{

    public function handle()
    {
        $client = new Client(getenv('TELEGRAM_BOT_TOKEN'));

        $client->on(function (Update $update) {
            $handlerClassName = MainMenu::class;
            $config = include(__DIR__ . '/config/command_handlers.php');

            if ($update->getCallbackQuery()) {
                $action = \json_decode($update->getCallbackQuery()->getData(), true)['a'];

                if (isset($config['callback_commands'][$action])) {
                    $handlerClassName = $config['callback_commands'][$action];
                }
            } else {
                if ($text = $update->getMessage()->getText()) {
                    if (str_starts_with($text, '/')) {
                        $handlers = $config['slash_commands'];
                    }

                    if (isset($handlers[$text])) {
                        $handlerClassName = $handlers[$text];
                    } else {
                        $key = $this->processKeyboardCommand($text);
                        $handlers = $config['keyboard_commands'];
                        if ($key && $handlers[$key]) {
                            $handlerClassName = $handlers[$key];
                        } else {
                            $handlers = $config['status_commands'];

                            $user = User::where('chat_id', $update->getMessage()->getFrom()->getId())->first();
                            if ($user && isset($handlers[$user->status])) {
                                $handlerClassName = $handlers[$user->status];
                            }
                        }
                    }

                }
            }

            (new $handlerClassName($update))->handle();
        }, function (Update $update) {
            return $update->getMessage() !== null || $update->getCallbackQuery() !== null;
        });

        $client->on(function (Update $update) {
            (new InlineQueryHandler($update))->handle();
            return true;
        }, function (Update $update) {
            return $update->getInlineQuery() !== null;
        });

        $client->run();
    }

    protected function processKeyboardCommand(string $text): ?string
    {
        $config = include('config/texts.php');
        $translations = @array_flip($config);
        if (isset($translations[$text])) {
            return $translations[$text];
        }

        return null;
    }

}
