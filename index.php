<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use src\Starting;

$parallel =
    Starting::parallel(
        array(
            array(
                "jobName" => 'jobs/job_1',
                "numberJobs" => 1,
                "shSizeForOneJob" => 300,
            ),
            array(
                "jobName" => 'jobs/job_2',
                "numberJobs" => 5,
                "shSizeForOneJob" => 90000,
                "dataPartitioning" => array(
                    "flagPartitioning" => 0,
                    "dataToPartitioning" => ['commit', 'sin']
                )
            ),
            array(
                "jobName" => 'jobs/job_4',
                "numberJobs" => 2,
                "shSizeForOneJob" => 300,
                "dataPartitioning" => array(
                    "flagPartitioning" => 1,
                    "dataToPartitioning" => ['commit', 'sin']
                )
            )
        )
    );

$output = $parallel->getOutput();
print_r($output);

Starting::singleAsyncProcess(
    array(
        "jobName" => 'jobs/async_1',
        "shSizeForOneJob" => 300,
        "data" => array(1, 2, 3)
    )
);

Starting::multipleAsyncProcesses(
    array(
        array(
            "jobName" => 'jobs/async_1',
            "numberJobs" => 3,
            "shSizeForOneJob" => 300,
            "dataPartitioning" => [
                "flagPartitioning" => 0,
                "dataToPartitioning" => array(1, 2, 3)
            ]
        ),
        array(
            "jobName" => 'jobs/async_2',
            "numberJobs" => 1,
            "shSizeForOneJob" => 300,
            "dataPartitioning" => [
                "flagPartitioning" => 0,
                "dataToPartitioning" => array('Hi')
            ]
        )
    )
);

echo "Я все";

