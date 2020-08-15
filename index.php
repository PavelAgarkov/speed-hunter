<?php

declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use src\ProcessesManager;

$Processes = new ProcessesManager();

$Processes
    ->configureProcessesLoop(
        [
            [
                0 => 'jobs/job_1',
                1 => 400,
                2 => 300,
                3 => [
                    0 => false,
                    [1, 2, 3, 4]
                ]
            ],
            [
                0 => 'jobs/job_2',
                1 => 10,
                2 => 30000,
                3 => [
                    0 => false,
                    1 => [10, 20, 30]
                ],
            ],
            [
                0 => 'jobs/job_4',
                1 => 3,
                2 => 50,
                3 => [
                    0 => false,
                    1 => ['commit'],
                ]
            ]
        ]
    )
    ->startProcessLoop()
    ->closeProcessLoop()
    ->clearResourcePool();

$output = $Processes->getOutputData();

$con = 0;
foreach ($output as $key => $item) {
    foreach ($item as $k => $v) {
        if (!empty($v)) {
            $con++;
        }
    }
}

print_r((string)$con);
print_r($output);
//print_r($Processes->getResourceMemoryData());

//phpinfo();
//print_r($_SERVER);
