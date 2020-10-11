<?php

namespace src\client\process\decorators;

use src\client\Client;

/**
 * Class OneAsyncProcessDecorator
 * @package src\process\running_process_decorator
 */
final class OneAsyncProcessDecorator extends Decorator
{
    public function execute(): void
    {
        $this->Client->oneAsyncProcessRun();
    }
}