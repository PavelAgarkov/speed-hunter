<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use src\Job;

Job::runJob(
    $argv,
    function (&$Job, $read): array {
        $array = [10];
        foreach (range(0, 9) as $key => $value) {
            $array[] = $value;
        }
        return $array;
    }
);