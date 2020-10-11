<?php

namespace src\client\process\decorators;

use src\client\Client;

/**
 * Class MultipleAsyncProcessesDecorator
 * @package src\process\running_process_decorator
 */
final class MultipleAsyncProcessesDecorator extends Decorator
{
    public function execute(): void
    {
        $this->Client->multipleAsyncProcessesRun();
    }
}