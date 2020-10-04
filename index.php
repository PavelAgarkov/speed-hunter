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
                4
            ),
            new MultipleProcessesSettings(
                "php7.4",
                'jobs/job_2',
                5,
                array(
                    "flagPartitioning" => 0,
                    "dataToPartitioning" => $data2 =  ['commit', 'sin', 'cod', 'cos', 'tan']
                ),
                Starting::weighData($data2)
            ),
            new MultipleProcessesSettings(
                "php7.4",
                'jobs/job_4',
                2,
                array(
                    "flagPartitioning" => 1,
                    "dataToPartitioning" => $data3 = ['commit', 'sin', 'cos']
                ),
                Starting::weighData($data3)
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
            $data4 =array(1, 2, 3),
            Starting::weighData($data4)
        )
    )
);

Starting::multipleAsyncProcesses(
    new SettingsList(
        new MultipleProcessesSettings(
            "php7.4",
            'jobs/async_1',
            3,
            array(
                "flagPartitioning" => 1,
                "dataToPartitioning" =>  $data5 = array(1, 2, 3)
            ),
            Starting::weighData($data5)
        ),
        new MultipleProcessesSettings(
            "php7.4",
            'jobs/async_2',
            3,
            array(
                "flagPartitioning" => 0,
                "dataToPartitioning" => $data6 = array('Hi')
            ),
            Starting::weighData($data5)
        )
    )
);

echo "Я все";

