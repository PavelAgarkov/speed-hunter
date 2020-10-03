<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use src\settings\MultipleProcessesSettings;
use src\settings\SettingsList;
use src\settings\SingleProcessSettings;
use src\Starting;

$parallel =
    Starting::parallel(
        new SettingsList(
            new MultipleProcessesSettings(
                "php7.4",
                'jobs/job_1',
                1,
                300
            ),
            new MultipleProcessesSettings(
                "php7.4",
                'jobs/job_2',
                5,
                90000,
                array(
                    "flagPartitioning" => 0,
                    "dataToPartitioning" => ['commit', 'sin']
                )
            ),
            new MultipleProcessesSettings(
                "php7.4",
                'jobs/job_4',
                2,
                300,
                array(
                    "flagPartitioning" => 1,
                    "dataToPartitioning" => ['commit', 'sin', 'cos']
                )
            )
        )
    );

$output = $parallel->getOutput();
print_r($output);

Starting::singleAsyncProcess(
    new SettingsList(
        new SingleProcessSettings(
            "php7.4",
            'jobs/async_1',
            300,
            array(1, 2, 3)
        )
    )
);

Starting::multipleAsyncProcesses(
    new SettingsList(
        new MultipleProcessesSettings(
            "php7.4",
            'jobs/async_1',
            3,
            300,
            array(
                "flagPartitioning" => 1,
                "dataToPartitioning" => array(1, 2, 3)
            )
        ),
        new MultipleProcessesSettings(
            "php7.4",
            'jobs/async_2',
            3,
            300,
            array(
                "flagPartitioning" => 0,
                "dataToPartitioning" => array('Hi')
            )
        )
    )
);

echo "Я все";

