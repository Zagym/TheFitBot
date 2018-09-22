<?php

namespace App;


use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Interfaces\TextChannelInterface as Channel;
use CharlotteDunois\Yasmin\Models\User;

abstract class AbstractCommand
{
    /** @var Message $message */
    protected $message;

    /** @var Channel $channel */
    protected $channel;

    /** @var User $author */
    protected $author;

    public function __construct(Message $message) {
        $this->message = $message;
        $this->channel = $message->channel;
        $this->author = $message->author;
    }

    abstract protected function load();
    abstract protected function help();
}
