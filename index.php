<?php

declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use src\ProcessesManager;

$Processes =
    ProcessesManager::runParallelJobs(
        array(
            "jobs/job_2" => [10, 20, 30],
            "jobs/job_4" => ['commit', 'sin'],
        ),
        "example.xml"
    );

$output = $Processes->getOutputData();

print_r($output);