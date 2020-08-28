<?php

declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

$starting = new \src\Starting();
$starting->parallel(
    array(
        array(
            "jobName" => 'jobs/job_1',
            "numberJobs" => 1,
            "shSizeForOneJob" => 300,
        ),
//        array(
//            "jobName" => 'jobs/job_2',
//            "numberJobs" => 1,
//            "shSizeForOneJob" => 30000,
//            "dataPartitioning" => array(
//                "flagPartitioning" => 0,
//                "dataToPartitioning" => ['commit', 'sin']
//            )
//        ),
        array(
            "jobName" => 'jobs/job_4',
            "numberJobs" => 2,
            "shSizeForOneJob" => 300,
            "dataPartitioning" => array(
                "flagPartitioning" => 0,
                "dataToPartitioning" => ['commit', 'sin']
            )
        )
    )
);
$output = $starting->getProcessManager()->getOutputData();

print_r($output);