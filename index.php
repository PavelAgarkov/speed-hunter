<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use src\settings\value_object\MultipleProcessesSettings;
use src\settings\SettingsList;
use src\settings\value_object\SingleProcessSettings;
use src\phpRoutine;

$parallelPhpRoutines =
    phpRoutine::parallel(
        new SettingsList(
            new MultipleProcessesSettings(
                "php7.4",
                'jobs/job_1',
                493
            ),
            new MultipleProcessesSettings(
                "php7.4",
                'jobs/job_2',
                5,
                array(
                    "flagPartitioning" => 0,
                    "dataToPartitioning" => $data2 =  ['commit', 'sin', 'cod', 'cos', 'tan']
                ),
                phpRoutine::weighData($data2)
            ),
            new MultipleProcessesSettings(
                "php7.4",
                'jobs/job_4',
                2,
                array(
                    "flagPartitioning" => 1,
                    "dataToPartitioning" => $data3 = ['commit', 'sin', 'cos']
                ),
                phpRoutine::weighData($data3)
            )
        )
    );

$output = $parallelPhpRoutines->getOutput();
print_r($output);

phpRoutine::singleAsyncProcess(
    new SettingsList(
        new SingleProcessSettings(
            "php7.4",
            'jobs/async_1',
            $data4 =array(1, 2, 3),
            phpRoutine::weighData($data4)
        )
    )
);

phpRoutine::multipleAsyncProcesses(
    new SettingsList(
        new MultipleProcessesSettings(
            "php7.4",
            'jobs/async_1',
            3,
            array(
                "flagPartitioning" => 1,
                "dataToPartitioning" =>  $data5 = array(1, 2, 3)
            ),
            phpRoutine::weighData($data5)
        ),
        new MultipleProcessesSettings(
            "php7.4",
            'jobs/async_2',
            3,
            array(
                "flagPartitioning" => 0,
                "dataToPartitioning" => $data6 = array('Hi')
            ),
            phpRoutine::weighData($data5)
        )
    )
);

echo "It's funny";

