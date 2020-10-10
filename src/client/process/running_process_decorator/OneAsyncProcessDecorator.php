<?php

namespace src\client\process\running_process_decorator;

use src\client\Client;

/**
 * Class OneAsyncProcessDecorator
 * @package src\process\running_process_decorator
 */
final class OneAsyncProcessDecorator extends Decorator
{
    /**
     * OneAsyncProcessDecorator constructor.
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
        $this->Client->oneAsyncProcessRun();
    }
}