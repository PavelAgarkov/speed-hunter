<?php

namespace src\client\planned_routines\single;

use src\client\Client;
use src\client\process\decorators\OneAsyncProcessDecorator;
use src\client\process\services\AsyncProcessService;

class AsyncSingleRoutine extends BaseSingleRoutine
{
    public function run(): void
    {
        new OneAsyncProcessDecorator(
            new Client(
                new AsyncProcessService(
                    null,
                    $this->settings
                )
            )
        );
    }
}