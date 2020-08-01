<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use src\SharedMemory;
use src\ProcessesManager;

// необходимое количество воркеров и участков разделяемой памяти
$countResourcesAndWorkers = 7;

// объем разделяемой памяти для записи информации из каждого воркера. Нужно подбирать сколько памяти необходимо
// для записи информации из воркера в kB
$memorySizeForOneWorker = 100000;

// инициализация разделяемой памяти
$SharedMemory = new SharedMemory();

// создание набора ресурсов в разделяемой памяти
$SharedMemory->createResourcePool(
    $countResourcesAndWorkers,
    $memorySizeForOneWorker
);

// инициализация менеджера процессов
$Processes = new ProcessesManager();

// запуск цикла, создающего процесс для каждого воркера
$Processes->startProcessLoop(
    $countResourcesAndWorkers,
    $SharedMemory->getResourcePool(),
    'subprocess.php',
    $memorySizeForOneWorker
);

// отключение всех процессов воркеров и каналов для связи
$Processes->closePipesAndProcesses($countResourcesAndWorkers);

// чтение записанных данных воркерами из разделяемой памяти, обход пула ресурсов и чтение из каждого ресурса
// с дальнейшей записью из всех ресурсов в один массив
$output = $SharedMemory->readAllDataFromResourcePool();

// удаление участка разделяемой памяти и информации об этом учатке из массива ресурсов
$delete = $SharedMemory->deleteAllDataFromResourcePool();

print_r($output);
$sum = 0;
foreach ($output as $key => $value) {
    $sum += $value[0];
}
echo $sum . PHP_EOL;
//1