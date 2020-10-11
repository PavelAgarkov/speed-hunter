<?php

namespace src\client\planned_routines\single;

use src\client\Client;
use src\client\process\decorators\OneProcessDecorator;
use src\client\process\services\ParallelProcessesService;

class SingleRoutine extends BaseSingleRoutine
{
    public function run(): Client
    {
        new OneProcessDecorator(
            $Client = new Client(
                new ParallelProcessesService(
                    null,
                    $this->settings
                )
            )
        );
        return $Client;
    }
}