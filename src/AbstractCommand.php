<?php

namespace App;

abstract class AbstractCommand
{
    abstract protected function load();
    abstract protected function help();
}
