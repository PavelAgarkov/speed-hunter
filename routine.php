<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use src\routine\php_routine\Routine;
use src\routine\php_routine\RoutineInterface;
use src\shared_memory\SharedMemoryManager;

$Routine = new Routine($argv);

$taskClass =
    $Routine
        ->getJob()
        ->getLaunchedJob()
        ->getJobName();

$task = new $taskClass();

if (!($task instanceof RoutineInterface)) {

    SharedMemoryManager::deleteSh(
        $Routine->getJob()->getSharedMemoryJob()->getSharedMemory(),
        $Routine->getJob()->getSharedMemoryJob()->getSharedMemoryResource()
    );

    exit("Class {$taskClass} can't will be create");
}

$task
    ->setRoutine($Routine)
    ->beforeExecute()
    ->execute()
    ->afterExecute();