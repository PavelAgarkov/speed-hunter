<?php

namespace src\client\process\running_process_decorator;

use src\client\Client;

/**
 * Class MultipleAsyncProcessesDecorator
 * @package src\process\running_process_decorator
 */
final class MultipleAsyncProcessesDecorator extends Decorator
{
    /**
     * MultipleAsyncProcessesDecorator constructor.
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
        $this->Client->multipleAsyncProcessesRun();
    }
}