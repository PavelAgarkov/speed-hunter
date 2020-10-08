<?php

namespace src\process\running_process_decorator;

use src\phpRoutine;


/**
 * Class Decorator
 * @package src\process\running_process_decorator
 */
abstract class Decorator
{
    /**
     * @var phpRoutine
     */
    public phpRoutine $phpRoutine;

    /**
     * Decorator constructor.
     * @param phpRoutine $phpRoutine
     */
    public function __construct(phpRoutine $phpRoutine)
    {
        $this->phpRoutine = $phpRoutine;
    }
}