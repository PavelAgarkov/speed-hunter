<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use src\client\settings\value_object\MultipleProcessesSettings;
use src\client\settings\SettingsList;
use src\client\settings\value_object\SingleProcessSettings;
use src\client\Client;

$parallelClient =
    Client::parallel(
        new SettingsList(
            new MultipleProcessesSettings(
                "php7.4 routine.php",
                "jobs\\\Job_1",
                5
            ),
            new MultipleProcessesSettings(
                "php7.4 routine.php",
                "jobs\\\Job_2",
                5,
                array(
                    "flagPartitioning" => 0,
                    "dataToPartitioning" => $data2 =  ['commit', 'sin', 'cod', 'cos', 'tan']
                ),
                Client::weighData($data2)
            ),
            new MultipleProcessesSettings(
                "php7.4 routine.php",
                "jobs\\\Job_4",
                3,
                array(
                    "flagPartitioning" => 1,
                    "dataToPartitioning" => $data3 = ['commit', 'sin', 'cos']
                ),
                Client::weighData($data3)
            )
        )
    );

$output = $parallelClient->getOutput();
print_r($output);

Client::singleAsyncProcess(
    new SettingsList(
        new SingleProcessSettings(
            "php7.4 routine.php",
            'jobs\\\Async_1',
            $data4 = array(1, 2, 3),
            Client::weighData($data4)
        )
    )
);

Client::multipleAsyncProcesses(
    new SettingsList(
        new MultipleProcessesSettings(
            "php7.4 routine.php",
            'jobs\\\Async_1',
            3,
            array(
                "flagPartitioning" => 1,
                "dataToPartitioning" =>  $data5 = array(1, 2, 3)
            ),
            Client::weighData($data5)
        ),
        new MultipleProcessesSettings(
            "php7.4 routine.php",
            'jobs\\\Async_2',
            3,
            array(
                "flagPartitioning" => 0,
                "dataToPartitioning" => $data6 = array('Hi')
            ),
            Client::weighData($data5)
        )
    )
);

echo "It's funny";

