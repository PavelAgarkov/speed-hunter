<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use src\ProcessesManager;

// инициализация менеджера процессов
$Processes = new ProcessesManager();

$Processes
    ->configureProcessesLoop(
        [
            [0 => 'workers/worker_1.php',   1 => 6, 2 => 100000],
            [0 => 'workers/worker_2.php',   1 => 2, 2 => 600],
            [0 => 'worker_3.php',           1 => 1, 2 => 400000]
        ]
    )
    ->startProcessLoop()
    ->closePipesAndProcesses()
    ->deleteAllDataFromResourcePool();

$output = $Processes->getOutputData();


print_r($output);
