<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

//$parallel =
//    \src\Starting::startingParallel(
//        array(
//            array(
//                "jobName" => 'jobs/job_1',
//                "numberJobs" => 1,
//                "shSizeForOneJob" => 300,
//            ),
//            array(
//                "jobName" => 'jobs/job_2',
//                "numberJobs" => 10,
//                "shSizeForOneJob" => 30000,
//                "dataPartitioning" => array(
//                    "flagPartitioning" => 0,
//                    "dataToPartitioning" => ['commit', 'sin']
//                )
//            ),
//            array(
//                "jobName" => 'jobs/job_4',
//                "numberJobs" => 2,
//                "shSizeForOneJob" => 300,
//                "dataPartitioning" => array(
//                    "flagPartitioning" => 1,
//                    "dataToPartitioning" => ['commit', 'sin']
//                )
//            )
//        )
//    );
//
//$output = $parallel->getOutput();
//print_r($output);

$async =
    \src\Starting::startingOneAsyncProcess(
        array(
            "jobName" => 'jobs/async_1',
            "shSizeForOneJob" => 300,
            "data" => array(1, 2, 3)
        )
    );

echo "Я все";

