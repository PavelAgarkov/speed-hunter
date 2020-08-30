<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use src\Job;

Job::runSingleAsyncJob(
    $argv,
    function (&$Job, $read) {
        sleep(5);
        $id = posix_getpid();
        $fp = fopen("t{$id}.txt", "w");
        fwrite($fp, " {$id} \r\n");
        fclose($fp);
    }
);

