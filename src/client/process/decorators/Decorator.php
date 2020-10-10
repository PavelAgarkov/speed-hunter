<?php

namespace src\client\process\decorators;

use src\client\Client;

/**
 * Class Decorator
 * @package src\process\running_process_decorator
 */
abstract class Decorator
{
    /**
     * @var Client
     */
    public Client $Client;

    /**
     * Decorator constructor.
     * @param Client $Client
     */
    public function __construct(Client $Client)
    {
        $this->Client = $Client;
    }
}