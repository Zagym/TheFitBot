<?php

namespace App;

use Discord\Parts\Channel\Message;
use Discord\Parts\Channel\Channel;

abstract class AbstractCommand
{
    /** @var Message $message */
    protected $message;

    /** @var Channel $channel */
    protected $channel;

    public function __construct(Message $message) {
        $this->message = $message;
        $this->channel = $message->channel;
    }

    abstract protected function load();
    abstract protected function help();
}
