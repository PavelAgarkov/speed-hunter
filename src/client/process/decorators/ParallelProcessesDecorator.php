<?php

namespace src\client\process\decorators;

use src\client\Client;
use src\client\process\decorators\Decorator;

/**
 * Class ParallelProcessesDecorator
 * @package src\process\running_process_decorator
 */
final class ParallelProcessesDecorator extends Decorator
{
    public function execute(): void
    {
        $this->Client->parallelRun();
    }
}