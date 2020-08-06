<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use src\Job;

Job::runJob(
    $argv,
    function (&$Job, $read): array {
        return [
            $read,
//            123,
//            3434343434
        ];
    }
);