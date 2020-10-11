<?php

namespace src\client\planned_routines\multiple;

use src\client\Client;
use src\client\planned_routines\BaseRoutine;
use src\client\process\decorators\MultipleAsyncProcessesDecorator;
use src\client\process\services\AsyncProcessService;

class AsyncRoutine extends BaseMultimpleRoutine
{
    public function run(): void
    {
        new MultipleAsyncProcessesDecorator(
            new Client(
                new AsyncProcessService(
                    $this->settingsList,
                    $this->settings
                )
            )
        );
    }
}