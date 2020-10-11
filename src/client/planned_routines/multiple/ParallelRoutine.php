<?php

namespace src\client\planned_routines\multiple;

use src\client\Client;
use src\client\planned_routines\multiple\BaseMultimpleRoutine;
use src\client\process\decorators\ParallelProcessesDecorator;
use src\client\process\services\ParallelProcessesService;

class ParallelRoutine extends BaseMultimpleRoutine
{
    public function run(): Client
    {
        new ParallelProcessesDecorator(
            $Client =
                new Client(
                    new ParallelProcessesService(
                        $this->settingsList,
                        $this->settings
                    )
                )
        );

        return $Client;
    }
}