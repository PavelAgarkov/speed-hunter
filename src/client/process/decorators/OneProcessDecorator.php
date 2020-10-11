<?php

namespace src\client\process\decorators;
use src\client\Client;

final class OneProcessDecorator extends Decorator
{
    public function execute(): void
    {
        $this->Client->oneProcessRun();
    }
}