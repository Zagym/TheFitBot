<?php

include __DIR__.'/vendor/autoload.php';

use Discord\Discord;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

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