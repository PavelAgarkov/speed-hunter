<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use src\client\Client;

Client::getSingleAsyncRoutine()
    ->indicateRoutine(
        [
            "phpPath" => "php7.4 routine.php",
            "jobName" => "jobs\\\Async_1",
            "data" => $data4 = [1, 2, 3],
            "shSizeForOneJob" => Client::weighData($data4)
        ]
    )
    ->run();

$singleClient =
    Client::getSingleRoutine()
        ->indicateRoutine(
            [
                "phpPath" => "php7.4 routine.php",
                "jobName" => "jobs\\\Job_2",
                "data" => $data2 = ['commit', 'sin', 'cod', 'cos', 'tan'],
                "shSizeForOneJob" => Client::weighData($data2)
            ]
        )
        ->run();

$output = $singleClient->getOutput();
print_r($output);

Client::getAsyncRoutine()
    ->addRoutine(
        [
            "phpPath" => "php7.4 routine.php",
            "jobName" => "jobs\\\Async_1",
            "numberJobs" => 3,
            "dataPartitioning" => [
                "flagPartitioning" => 0,
                "dataToPartitioning" => $data6 = array('Hi')
            ],
            "shSizeForOneJob" => Client::weighData($data6)
        ]
    )
    ->addRoutine(
        [
            "phpPath" => "php7.4 routine.php",
            "jobName" => "jobs\\\Async_2",
            "numberJobs" => 3,
            "dataPartitioning" => [
                "flagPartitioning" => 0,
                "dataToPartitioning" => $data6 = array('Hi')
            ],
            "shSizeForOneJob" => Client::weighData($data6)
        ]
    )
    ->run();

$parallelRoutine =
    Client::getParallelRoutine()
        ->addRoutine(
            [
                "phpPath" => "php7.4 routine.php",
                "jobName" => "jobs\\\Job_1",
                "numberJobs" => 2
            ]
        )
        ->addRoutine(
            [
                "phpPath" => "php7.4 routine.php",
                "jobName" => "jobs\\\Job_2",
                "numberJobs" => 2,
                "dataPartitioning" => [
                    "flagPartitioning" => 1,
                    "dataToPartitioning" => $data6 = ['commit', 'sin', 'cod', 'cos', 'tan']
                ],
                "shSizeForOneJob" => Client::weighData($data6)
            ]
        )
        ->addRoutine(
            [
                "phpPath" => "php7.4 routine.php",
                "jobName" => "jobs\\\Job_4",
                "numberJobs" => 5,
                "dataPartitioning" => [
                    "flagPartitioning" => 1,
                    "dataToPartitioning" => $data7 = ['commit', 'sin', 'cod', 'cos', 'tan']
                ],
                "shSizeForOneJob" => Client::weighData($data7)
            ]
        )
        ->run();

$output = $parallelRoutine->getOutput();
print_r($output);

echo "It's so funny";

