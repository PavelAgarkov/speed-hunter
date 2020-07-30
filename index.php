<?php

// non-blocking-proc_open.php
// File descriptors for each subprocess.

$login = posix_getuid();

$start = microtime(true);

//--------------------------------------

$sharedMemorySize = 1000000; //  объём выделяемой разделяемой памяти
$streamPool = [];
$output = [];

//--------------------------------------

// Подключаем разделяемую память

while (count($streamPool) < 5) {
    //  ключ разделяемой памяти
    $sharedMemoryKey = rand(100, 1000000);
    $sharedMemoryId = shmop_open($sharedMemoryKey, "n", 0755, $sharedMemorySize);

    // если разделяемая память по данному ключу занята, то делаем новый ключ
    if ($sharedMemoryId === false) {
        echo "Ошибка при подключении разделяемой памяти.\n";
        $sharedMemoryKey = rand(100, 1000000);
        // иначе записываем в пул памяти
    } else {
        $memory = (string)$sharedMemoryId;
        $memoryNumber = preg_replace('/[^0-9]/', '', explode(' ', $memory)[2]);
        $streamPool[][$memoryNumber] = [
            $sharedMemoryId,
            $sharedMemoryKey
        ];
    }
}


echo "Старт.\n";

//--------------------------------------

$descriptors = [
    0 => ['pipe', 'r'], // stdin
    1 => ['pipe', 'w'], // stdout,
    2 => array("file", "/tmp/error-output.txt", "a")
];
$pipes = [];
$processes = [];
$procPipes = [];

foreach (range(0, 4) as $key => $i) {
    // Spawn a subprocess.

    echo "Fork $i process <br>";
    $numberMemoryKey = current($streamPool[$key])[1];

//    $proc = proc_open('php subprocess.php ' . $i . ' ' . $sharedMemoryId . ' ' . $sem_id, $descriptors, $procPipes);
//    $proc = proc_open("php subprocess.php {$i} {$memoryNumber} {$semoforeNumber}" , $descriptors, $procPipes);
//    $proc = proc_open("php subprocess.php {$i} {$memoryNumber}", $descriptors, $procPipes);
    $proc = proc_open("php subprocess.php {$i} {$numberMemoryKey}", $descriptors, $procPipes);

    $processes[$i] = $proc;

    // Make the subprocess non-blocking (only output pipe).
    stream_set_blocking($procPipes[1], 0);
    $pipes[$i] = $procPipes;

    echo "Review $i process <br>";
}


// Run in a loop until all subprocesses finish.
//while (array_filter($processes, function ($proc) {
//    return proc_get_status($proc)['running'];
//})) {
//    foreach (range(1, 1) as $i) {
//        // Read all available output (unread output is buffered).
//
//        $str = fread($pipes[$i][1], 8192);
//
//        if ($str) {
//            printf($str);
//        }
//    }
//}


// Close all pipes and processes.
foreach (range(0, 4) as $i) {
    echo "Close output process from piping $i <br>";
    fclose($pipes[$i][1]);
    proc_close($processes[$i]);
}

// читаем что записали в память в воркере
foreach (range(0, 4) as $key => $value) {
    $numberMemoryKey = current($streamPool[$key])[0];
    echo "<br>" . $numberMemoryKey . "<br>";
//    $stream = shmop_open($numberMemoryKey, "a", 0755, 20000);
    $read = shmop_read($numberMemoryKey, 0, shmop_size($numberMemoryKey));
    $unserialise = unserialize($read);
//    if ($read === false) {
//        echo '<br>' . 'при чтении произлошла ошибка' . '<br>';
//    }

    $output[] = $unserialise;
}

//--------------------------------------

// Удаляем сегмент разделяемой памяти
foreach (range(0, 4) as $key => $value) {
    $numberMemoryKey = current($streamPool[$key])[0];
    $delete = shmop_delete($numberMemoryKey);
//    if ($delete === true) {
//        echo PHP_EOL. 'удалось удалить сегмент памяти' . PHP_EOL;
//    } else {
//        echo 'не удалось' . PHP_EOL;
//    }
}


//--------------------------------------

$time = microtime(true) - $start;

echo $time . "<br>";

print_r($output);

