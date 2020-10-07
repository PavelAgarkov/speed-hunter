<?php

namespace src\process\running_process_decorator;

use src\Starting;

/**
 * Class Decorator
 * @package src\process\running_process_decorator
 */
abstract class Decorator
{
    /**
     * @var Starting
     */
    public Starting $starting;

    /**
     * Decorator constructor.
     * @param Starting $starting
     */
    public function __construct(Starting $starting)
    {
        $this->starting = $starting;
    }
}