<?php

declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use src\ProcessesManager;

$Processes = new ProcessesManager();

$Processes
    ->configureProcessesLoop(
        [
            [
                0 => 'workers/worker_1',
                1 => 10,
                2 => 300,
                3 => [
                    0 => false,
                    [1, 2, 3, 4]
                ]
            ],
            [
                0 => 'workers/worker_2',
                1 => 10,
                2 => 30000,
                3 => [
                    0 => false,
                    1 => [10, 20, 30]
                ],
            ],
            [
                0 => 'workers/worker_4',
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
print_r($output);
