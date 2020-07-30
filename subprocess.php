<?php
declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use src\SharedMemory;

$keyMemory = (int)$argv[2];

$SharedMemory = new SharedMemory();

$memory = $SharedMemory->open(
    $keyMemory,
    "w",
    1000
);

$array = [3];

$serialize = serialize($array);

$SharedMemory->write(
    $memory,
    $array,
    0
);
$usage = memory_get_usage(true);

