<?php

namespace App\Utils;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class Twig
{

    private static FilesystemLoader $loader;

    private static $environment;

    public static function getInstance(): Environment
    {
        if (is_null(self::$environment)) {
            self::$loader = new FilesystemLoader(__DIR__ . '/../../templates');
            self::$environment = new Environment(self::$loader, [
                'debug' => true
            ]);
        }

        self::$environment->addExtension(new DebugExtension());

        return self::$environment;
    }

}
