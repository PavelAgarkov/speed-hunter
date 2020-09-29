<?php

namespace src\process\running_process_decorator;

use src\process\running_process_decorator\Decorator;
use src\Starting;

/**
 * Class ParallelProcessesDecorator
 * @package src\process\running_process_decorator
 */
class ParallelProcessesDecorator extends Decorator
{
    /**
     * ParallelProcessesDecorator constructor.
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
        $this->starting->parallelRun();
    }
}