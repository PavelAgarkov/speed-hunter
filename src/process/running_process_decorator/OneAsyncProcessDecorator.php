<?php

namespace src\process\running_process_decorator;

use src\Starting;

/**
 * Class OneAsyncProcessDecorator
 * @package src\process\running_process_decorator
 */
final class OneAsyncProcessDecorator extends Decorator
{
    /**
     * OneAsyncProcessDecorator constructor.
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
        $this->starting->oneAsyncProcessRun();
    }
}