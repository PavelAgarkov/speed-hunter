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
            // если не указан 3 элемент, то в воркер не передаются данные(если воркер один)
            // в элементе под ключом 3 хранится массив, 0 => разделять ли данные между воркерами
            // true - разделять, false - не разделять, а передать в каждый воркер общие данные
            [
                0 => 'workers/worker_1',
                1 => 10,
                2 => 300,
                3 => [
                    0 => false,
                    [1, 2, 3, 4]
                ]
            ],
            [
                0 => 'workers/worker_2',
                1 => 4,
                2 => 30000,
                3 => [
                    0 => false,
                    1 => [10, 20, 30]
                ],
            ],
            [
                0 => 'workers/worker_4',
                1 => 3,
                2 => 50,
                3 => [
                    0 => false,
                    1 => ['commit'],
                ]
            ]
        ]
    )
    ->startProcessLoop()
    ->closeProcessLoop()
    ->clearResourcePool();

// результат работы параллельных воркеров
//$output = $Processes->getOutputData('workers/worker_4');
$output = $Processes->getOutputData();

$memory = $Processes->getResourceMemoryData();

print_r($output);
