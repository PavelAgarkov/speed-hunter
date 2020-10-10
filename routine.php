<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/vendor/autoload.php';

use src\routine\Routine;
use src\routine\RoutineInterface;

$Routine = new Routine($argv);
$taskClass =
    $Routine
        ->getJob()
        ->getLaunchedJob()
        ->getJobName();
$task = new $taskClass();

if (!($task instanceof RoutineInterface)) {
    exit("Class {$taskClass} can't will be create");
}

$task
    ->setRoutine($Routine)
    ->beforeExecute()
    ->execute()
    ->afterExecute();