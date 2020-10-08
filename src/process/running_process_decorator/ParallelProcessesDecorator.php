<?php

namespace src\process\running_process_decorator;

use src\phpRoutine;
use src\process\running_process_decorator\Decorator;

/**
 * Class ParallelProcessesDecorator
 * @package src\process\running_process_decorator
 */
final class ParallelProcessesDecorator extends Decorator
{
    /**
     * ParallelProcessesDecorator constructor.
     * @param phpRoutine $phpRoutine
     */
    public function __construct(phpRoutine $phpRoutine)
    {
        parent::__construct($phpRoutine);
        $this->execute();
    }

    /**
     *
     */
    public function execute(): void
    {
        $this->phpRoutine->parallelRun();
    }
}