<?php

namespace Phi\Console;


class CommandFilter
{

    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }


    public function execute(Command $command)
    {
        return call_user_func_array($this->callback, array($command));
    }

}