<?php

include __DIR__.'/vendor/autoload.php';

use Discord\Discord;
use Medoo\Medoo;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$database = new Medoo([
    'database_type' => getenv('DB_CONNECTION'),
    'database_name' => getenv('DB_DATABASE'),
    'server' => getenv('DB_HOST'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
]);

$discord = new Discord([
    'token' => getenv('BOT_TOKEN'),
]);

$discord->on('ready', function ($discord) {
    echo "Bot is ready!", PHP_EOL;

    /**
     * @var \Discord\Parts\Channel\Message $message
     * @var Discord $discord
     */
    $discord->on('message', function ($message, $discord) {
        new App\LoaderCommand($message);
    });
});

$discord->run();