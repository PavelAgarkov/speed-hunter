<?php
// non-blocking-proc_open.php
// File descriptors for each subprocess.

$start = microtime(true);

//--------------------------------------

$MEMSIZE = 512; //  объём выделяемой разделяемой памяти
$SEMKEY = 1;   //  ключ семафора
$SHMKEY = 2;   //  ключ разделяемой памяти

echo "Старт.\n";

// Создаем семафор
$sem_id = sem_get($SEMKEY, 1);
if ($sem_id === false)
{
    echo "Ошибка при создании семафора";
    exit;
}
else
    echo "Создан семафор $sem_id.\n";

//--------------------------------------

// Занимаем семафор
if (! sem_acquire($sem_id))
{
    echo "Ошибка при попытке занять семафор $sem_id.\n";
    sem_remove($sem_id);
    exit;
}
else
    echo "Успешно занят семафор $sem_id.\n";

//--------------------------------------

// Подключаем разделяемую память
$shm_id = shm_attach($SHMKEY, $MEMSIZE);

if ($shm_id === false)
{
    echo "Ошибка при подключении разделяемой памяти.\n";
    sem_remove($sem_id);
    exit;
}
else
    echo "Успешное подключение разделяемой памяти: $shm_id.\n";

//--------------------------------------

$descriptors = [
    0 => ['pipe', 'r'], // stdin
    1 => ['pipe', 'w'], // stdout,
    2 => array("file", "/tmp/error-output.txt", "a")
];

$pipes = [];
$processes = [];
$memory = (string) $shm_id;
$semofore = (string) $sem_id;

$memoryNumber = preg_replace('/[^0-9]/', '', explode(' ', $memory)[2]);
$semoforeNumber = preg_replace('/[^0-9]/', '', explode(' ', $semofore)[2]);

//if (!shm_put_var($shm_id, 1, [0 => $shm_id, 1 => $sem_id]))
//{
//    echo "Ошибка при попытке записать переменную 1 в разделяемую память $shm_id.\n";
//
//    // Овобождаем ресурсы.
//    sem_remove($sem_id);
//    shm_remove($shm_id);
//    exit;
//}
//else
//    echo "Переменная 1 записана в разделяемую память.\n";



foreach (range(1, 5) as $i) {
    // Spawn a subprocess.

    echo "Fork $i process <br>";

//    $proc = proc_open('php subprocess.php ' . $i . ' ' . $shm_id . ' ' . $sem_id, $descriptors, $procPipes);
    $proc = proc_open("php subprocess.php {$i} {$memoryNumber} {$semoforeNumber}" , $descriptors, $procPipes);
    $processes[$i] = $proc;
    $res = get_resources();

    // Make the subprocess non-blocking (only output pipe).
    stream_set_blocking($procPipes[1], 0);
    $pipes[$i] = $procPipes;

    echo "Review $i process <br>";
}


// Run in a loop until all subprocesses finish.
while (array_filter($processes, function ($proc) {
    return proc_get_status($proc)['running'];
})) {
    foreach (range(1, 1) as $i) {
//        usleep(10 * 1000); // 100ms
        // Read all available output (unread output is buffered).

        $str = fread($pipes[$i][1], 8192);

        if ($str) {
            printf($str);
        }
    }
}




// Close all pipes and processes.
foreach (range(1, 5) as $i) {
    echo "Close output process from piping $i <br>";
    fclose($pipes[$i][1]);
    proc_close($processes[$i]);
}

// читаем что записали в память в воркере

foreach (range(1, 5) as $key => $value) {
    $a = shm_get_var($shm_id, $value);
    print_r($a);
}

//--------------------------------------

// Освобождаем семафор
if (!sem_release($sem_id))
    echo "Ошибка при попытке освободить семафор $sem_id.\n";
else
    echo "Семафор $sem_id освобожден.\n";

// Удаляем сегмент разделяемой памяти
if (shm_remove ($shm_id))
    echo "Сегмент разделяемой памяти успешно удален.\n";
else
    echo "Ошибка при попытке удалить сегмент разделяемой памяти $shm_id.\n";

// Удаляем семафор.
if (sem_remove($sem_id))
    echo "Семафор успешно удален.\n";
else
    echo "Ошибка при попытке удалить семафор $sem_id.\n";

echo "Конец.\n";


//--------------------------------------


//foreach (range(1, 5) as $i) {
//    $ch = curl_init("https://api.github.com/repos/PavelAgarkov/pocket-typing/events");
//    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
////    curl_setopt($ch, CURLOPT_HEADER, 1);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    $cont = curl_exec($ch);
//
//    curl_close($ch);
//}

$time = microtime(true) - $start;

echo $time;






// Пишем переменную 1
//if (!shm_put_var($shm_id, 1, [0 => 'переменная 1']))
//{
//    echo "Ошибка при попытке записать переменную 1 в разделяемую память $shm_id.\n";
//
//    // Овобождаем ресурсы.
//    sem_remove($sem_id);
//    shm_remove($shm_id);
//    exit;
//}
//else
//    echo "Переменная 1 записана в разделяемую память.\n";
//
//// Пишем переменную 2
//if (!shm_put_var($shm_id, 2, "Переменная 2"))
//{
//    echo "Ошибка при попытке записать переменную 2 в разделяемую память $shm_id.\n";
//
//    // Освобождаем ресурсы.
//    sem_remove($sem_id);
//    shm_remove ($shm_id);
//    exit;
//}
//else
//    echo "Переменная 2 записана в разделяемую память.\n";

// Читаем переменную 1
//$var1 = shm_get_var($shm_id, 1);
//if ($var1 === false)
//{
//    echo "Ошибка при попытке прочитать переменную 1 из разделяемой памяти $shm_id, " .
//        "возвращенное значение=$var1.\n";
//}
//else
//    echo "Прочитана переменная 1=$var1.\n";
//
//// Читаем переменную 2
//$var2 = shm_get_var ($shm_id, 2);
//if ($var1 === false)
//{
//    echo "Ошибка при попытке прочитать переменную 2 из разделяемой памяти $shm_id, " .
//        "возвращенное значение=$var2.\n";
//}
//else
//    echo "Прочитана переменная 2=$var2.\n";


