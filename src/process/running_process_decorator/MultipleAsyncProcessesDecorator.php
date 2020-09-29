<?php

namespace src\process\running_process_decorator;

use src\Starting;

/**
 * Class MultipleAsyncProcessesDecorator
 * @package src\process\running_process_decorator
 */
class MultipleAsyncProcessesDecorator extends Decorator
{
    /**
     * MultipleAsyncProcessesDecorator constructor.
     * @param Starting $starting
     */
    public function __construct(Starting $starting)
    {
        parent::__construct($starting);
        $this->execute();
    }

    /**
     *
     */
    public function execute(): void
    {
        $this->starting->multipleAsyncProcessesRun();
    }
}