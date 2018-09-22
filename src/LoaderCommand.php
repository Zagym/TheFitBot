<?php

namespace App;

use App\Bank\Register;
use Discord\Parts\Channel\Message;

class LoaderCommand
{
    private $prefix = '?';
    private $message;

    public function __construct(Message $message)
    {
        if ($message->content[0] != $this->prefix) {
            return;
        }

        $this->message = $message;
        $this->load();
    }

    private function load()
    {
        $command = substr($this->message->content, 1);

        if (array_key_exists($command, self::commands())) {
            $class = self::commands()[$command];
            new $class($this->message);
        }
    }

    private static function commands()
    {
        return [
            'register' => Register::class,
        ];
    }
}
