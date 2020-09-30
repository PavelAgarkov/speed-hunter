<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use src\job\Job;

Job::runSingleAsyncJob(
    $argv,
    function (&$Job, $read) {
        sleep(1);
        $id = posix_getpid();
        $fp = fopen("t{$id}.txt", "w");
        fwrite($fp, " {$read[0]} \r\n");
        fclose($fp);
    }
);

