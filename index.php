<?php

declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use src\ProcessesManager;

$Processes =
    ProcessesManager::runParallelJobs(
        array(
            array(
                "jobName" => 'jobs/job_1',
                "numberJobs" => 485,
                "shSizeForOneJob" => 300,
            ),
            array(
                "jobName" => 'jobs/job_2',
                "numberJobs" => 5,
                "shSizeForOneJob" => 30000,
                "dataPartitioning" => array(
                    "flagPartitioning" => 0,
                    "dataToPartitioning" => ['commit', 'sin']
                )
            ),
            array(
                "jobName" => 'jobs/job_4',
                "numberJobs" => 10,
                "shSizeForOneJob" => 300,
                "dataPartitioning" => array(
                    "flagPartitioning" => 0,
                    "dataToPartitioning" => ['commit', 'sin']
                )
            )
        )
    );

$output = $Processes->getOutputData();

print_r($output);