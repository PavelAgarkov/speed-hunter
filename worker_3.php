<?php


declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use src\SharedMemory;

// получение ключа разделяемой памяти из вызывающей оболочки
$keyMemory = (int)$argv[2];

//объем выделенной разделяемой памяти на запись данных из одиного воркера
$memoryValue = (int)$argv[3];

// инициализация объекта разделяемой памяти
$SharedMemory = new SharedMemory();

// получение доступа на чтение к участку разделяемой памяти по ключу
$memory = $SharedMemory->open(
    $keyMemory,
    "w",
    $memoryValue
);

//------------------------- логика воркера -------------------------//
$array = [10];

//$ch = curl_init("https://api.github.com/repos/PavelAgarkov/pocket-typing/events");
//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36');
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//$cont = curl_exec($ch);
//curl_close($ch);
//$array = json_decode($cont, true);

//------------------------- логика воркера -------------------------//

// сериализация полученных в воркере данных
$serialize = serialize($array);

// запись в разделяемую память сериализованных данных
$SharedMemory->write(
    $memory,
    $array,
    0
);

// подсчет памяти затрачивамой скриптом
$usage = memory_get_usage(true);

// демонстрация каналов для отладки
//printf(PHP_EOL . " worker {$keyMemory}" . PHP_EOL);
