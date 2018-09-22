<?php

include __DIR__.'/vendor/autoload.php';

use React\EventLoop\Factory;
use CharlotteDunois\Yasmin\Client;

$loop = Factory::create();
try {
    $discord = new Client(array(), $loop);
} catch (Exception $e) {
    echo $e->getMessage();
}

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$discord->on('ready', function () use ($discord) {
    echo "Bot is ready!", PHP_EOL;
});

$discord->on('message', function ($message) {
    /** @var \CharlotteDunois\Yasmin\Models\Message $message */
    new App\LoaderCommand($message);
});

$discord->login(getenv('BOT_TOKEN'));
$loop->run();