<?php

declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use src\ProcessesManager;

$Processes =
    ProcessesManager::runParallelJobs([
        [
            "jobName" => 'jobs/job_1',
            "numberJobs" => 485,
            "shSizeForOneJob" => 300,
        ],
        [
            "jobName" => 'jobs/job_2',
            "numberJobs" => 5,
            "shSizeForOneJob" => 30000,
            "dataPartitioning" => [
                "flagPartitioning" => false,
                "dataToPartitioning" => [10, 20, 30]
            ],
        ],
        [
            "jobName" => 'jobs/job_4',
            "numberJobs" => 10,
            "shSizeForOneJob" => 300,
            "dataPartitioning" => [
                "flagPartitioning" => false,
                "dataToPartitioning" => ['commit'],
            ]
        ]
    ]);

$output = $Processes->getOutputData();

print_r($output);
