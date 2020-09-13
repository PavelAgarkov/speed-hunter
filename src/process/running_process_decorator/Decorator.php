<?php

namespace src\process\running_process_decorator;

use src\Starting;

class Decorator
{
    public Starting $starting;

    public function __construct(Starting $starting)
    {
        $this->starting = $starting;
    }
}