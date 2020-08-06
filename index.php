<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use src\ProcessesManager;

// инициализация менеджера процессов
$Processes = new ProcessesManager();

$Processes
    //конфигурирование цикла процессов
    ->configureProcessesLoop(
        // перечисление воркеров для конфигурирования цикла процессов
        [
            // принимает массив конфигураций, который содержит
            // 0 - путь до файла воркера, 1 - количество воркеров,
            // 2 - память в килобайтах выделенная на один воркер,
            // 3 - массив данных необходимых для параллельной обработки
            // если не указан 3 элемент, то в воркер не передаются данные
            [
                0 => 'workers/worker_1',
                1 => 1000,
                2 => 300000,
//                3 => [1, 2, 3, 4]
            ],
            [
                0 => 'workers/worker_2',
                1 => 1,
                2 => 50,
//                3 => [10, 20, 30],
            ],
            [
                0 => 'worker_3',
                1 => 1,
                2 => 30,
//                3 => ['a'],
            ],
            [
                0 => 'workers/worker_4',
                1 => 3,
                2 => 500,
//                3 => ['commit'],
            ]
        ]
    )
    ->startProcessLoop()
    ->closeProcessLoop()
    ->clearResourcePool();

// результат работы параллельных воркеров
//$output = $Processes->getOutputData('workers/worker_4');
$output = $Processes->getOutputData();

print_r($output);
