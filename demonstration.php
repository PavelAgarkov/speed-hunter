<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use src\SharedMemory;
use src\Processes;

$SharedMemory = new SharedMemory();
$SharedMemory->createResourcePool(
    5,
    1000
);

$Processes = new Processes();
$Processes->startProcessLoop(
    5,
    $SharedMemory->getResourcePool(),
    'subprocess.php'
);

$Processes->closePipesAndProcesses(5);

$output = $SharedMemory->readAllDataFromResourcePool();

$delete = $SharedMemory->deleteAllDataFromResourcePool();

print_r($output);
$sum = 0;
foreach ($output as $key => $value) {
    $sum += $value[0];
}
echo $sum . PHP_EOL;