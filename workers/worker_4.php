<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use src\Job;

$Job = new Job($argv);

$Job->restoreSharedMemoryResource("w");

$read = $Job->readFromSharedMemoryResource();

$array = $Job->handler(
    function (&$Job, $read): array {
        return [$read, 123, 3434343434];
    },
    $read
);

$Job->writeIntoSharedMemoryResource($array);

$usage = memory_get_usage(true);