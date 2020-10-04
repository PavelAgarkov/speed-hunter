<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use src\job\Job;

Job::runSingleAsyncJob(
    $argv,
    function (&$Job, $read) {
//        sleep(1);
        $id = posix_getpid();
        $fp = fopen("t{$id}.txt", "w");
        $str = implode(',', $read);
        fwrite($fp, " {$str} \r\n");
        fclose($fp);
    }
);

