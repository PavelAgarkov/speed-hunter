<?php

namespace src\client\process\running_process_decorator;

use src\client\Client;
use src\client\process\running_process_decorator\Decorator;

/**
 * Class ParallelProcessesDecorator
 * @package src\process\running_process_decorator
 */
final class ParallelProcessesDecorator extends Decorator
{
    /**
     * ParallelProcessesDecorator constructor.
     * @param Client $Client
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);
        $this->execute();
    }

    /**
     *
     */
    public function execute(): void
    {
        $this->Client->parallelRun();
    }
}