Запуск мультизадачной обработки во время обработки запроса на сервере.
Пакет работает только на unix подобных OC.

   Пакет работает с разделяемой памятью unix. Исходя из необходимого количества воркеров и необходимой памяти
для записи данных из них создается набор ресурсов разделяемой памяти unix. Затем открываются дочерние процессы,
в которые передается информация об участке разделяемой памяти для воркера.
   Создаваемый воркер получает ключ от участка разделяемой памяти, имеющий указанный размер. После чего
восстанавливает подключение к памяти с флагом на запись и записывает сериализованные данные. После чего
в основной процесс, по завершению выполнения каждого воркера, передается управление. Затем закрываются открытые
каналы и процессы, читаются данные из всех участков разделяемой памяти,очищается занятая unix разделяемая память.

Файлы:
 - index.php является точкой входа для cgi или cli;
 - subprocess.php - воркер(дочерний процесс) запускаемый родительским процессом, как на сервере, так и при вызове
   через php-cli. 

Пакет применим как серверный или консольный скрипт.

Код для запуска.

```php
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
                0 => 'workers/worker_1.php',
                1 => 4,
                2 => 30,
//                3 => [1, 2, 3, 4]
            ],
            [
                0 => 'workers/worker_2.php',
                1 => 2,
                2 => 30,
                3 => [10, 20, 30],
            ],
            [
                0 => 'worker_3.php',
                1 => 1,
                2 => 30,
                3 => ['a'],
            ]
        ]
    )
    ->startProcessLoop()
    ->closeProcessLoop()
    ->clearResourcePool();

// результат работы параллельных воркеров
$output = $Processes->getOutputData('workers/worker_1.php');
```
