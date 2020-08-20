<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use src\Job;

Job::runJob(
    $argv,
    function (&$Job, $read): array {
        $ch = curl_init("https://api.github.com/repos/PavelAgarkov/pocket-typing/events");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $cont = curl_exec($ch);
        curl_close($ch);

        $array = json_decode($cont, true);
        return [$array, $read];
    }
);
