<?php

namespace src\process\running_process_decorator;

use src\phpRoutine;

/**
 * Class OneAsyncProcessDecorator
 * @package src\process\running_process_decorator
 */
final class OneAsyncProcessDecorator extends Decorator
{
    /**
     * OneAsyncProcessDecorator constructor.
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
        $this->phpRoutine->oneAsyncProcessRun();
    }
}