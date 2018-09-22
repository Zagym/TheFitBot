<?php

namespace App;


use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Interfaces\TextChannelInterface as Channel;

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
