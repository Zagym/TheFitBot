<?php

namespace App;

use App\Bank\Balance;
use App\Bank\Register;
use CharlotteDunois\Yasmin\Models\Message;

class LoaderCommand
{
    private $prefix = '?';
    private $message;

    public function __construct(Message $message)
    {
        if ($message->content[0] != $this->prefix) {
            return null;
        }

        $this->message = $message;
        $this->load();
    }

    private function load()
    {
        $command = substr($this->message->content, 1);
        $command = explode(' ', $command);

        if (array_key_exists($command[0], self::commands())) {
            $class = self::commands()[$command[0]];
            new $class($this->message);
        }
    }

    private static function commands()
    {
        return [
            'register' => Register::class,
            'balance' => Balance::class,
        ];
    }
}
