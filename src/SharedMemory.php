<?php

namespace src;

/** Класс управления разделяемой памятью unix для параллельной работы процессов.
 * Class SharedMemory
 * @package src
 */
class SharedMemory
{
    /**
     * @var array - закрытый массив для записи ресурсов, представляющих собой информацию
     * о созданных участках разделяемой памяти.
     */
    private array $resourcePool = [];

    /**
     * @var array - набор ресурсов для записи дополнительных настроек
     */
    private array $resourcePoolСonfirations = [];

    /**
     * @var array - набор воркеров
     */
    private array $poolOfWorkers;

    /**
     * @var int - количество ресурсов разделяемой памяти для создания.
     */
    private int $countResources;

    /**
     * @var array - массив, в который записываются данные из каждого ресурса разделяемой памяти.
     */
    private array $output;

    public function __construct()
    {
    }

    /** Метод создает набор участков разделяемой памяти и записываеи их в массив.
     * @param int $countResources - количество ресурсов необходимые к созданию, исходя их количества воркеров
     *  в клиентском коде.
     * @param int $memorySize - размер разделяемой памяти для каждого участка управляемой памяти.
     */
//    public function createResourcePool(int $countResources = 0, int $memorySize = 0)
    public function createResourcePool(array $poolOfWorkers)
    {
        $this->poolOfWorkers = $poolOfWorkers;

        foreach ($poolOfWorkers as $key => $worker) {

            $workerName = $worker->getWorkerName();
            $count = $worker->getCountWorkers();
            $memorySize = $worker->getMemorySize();

            $this->resourcePoolСonfirations[$workerName]['workerName'] = $workerName;
            $this->resourcePoolСonfirations[$workerName]['countWorkers'] = $count;
            $this->resourcePoolСonfirations[$workerName]['memorySize'] = $memorySize;

            $this->resourcePool[$workerName] = [];


            while (count($this->resourcePool[$workerName]) < $count) {
                //  ключ разделяемой памяти
                $sharedMemoryKey = rand(100, 9000000);
                //флаг "n" говорит, что создается новый участок общей памяти и возвращает false если участок с таким ключом уже есть
                // permission 0755
                $sharedMemoryResource = $this->open($sharedMemoryKey, "n", $memorySize);

                // если разделяемая память по данному ключу занята, то делаем новый ключ, иначе записываем в пул памяти
                $this->addInResourcePool($sharedMemoryResource, $sharedMemoryKey, $workerName);
            }
        }
    }

    /** Метод создающий участок разделяемой памяти.
     * @param int $memoryKey - ключ разделяемой памяти.
     * @param string $openFlag - флаг для открытия ресурса с определенными набором возможностей.
     * @param int $size - объем памяти в kB для одного учатка.
     * @return resource
     */
    public function open(int $memoryKey, string $openFlag = "n", int $size = 0)
    {
        $sharedMemoryResource = shmop_open($memoryKey, $openFlag, 0755, $size);
        return $sharedMemoryResource;
    }

    /** Метод проверяющий занят ли участок разделяемой памяти с данным ключом.
     *  Если участок занят, то формируется новый ключ из случайного интервала.
     *  Если участок свободен, то информация о созданном участке записывается
     *  в массив.
     * @param resource $sharedMemoryResource - ресурс разделяемой памяти.
     * @param int $sharedMemoryKey - ключ разделяемой памяти.
     */
    private function addInResourcePool($sharedMemoryResource, int $sharedMemoryKey, string $workerName): void
    {
        if ($sharedMemoryResource === false) {
            $sharedMemoryKey = rand(100, 9000000);
        } else {
            $memory = (string)$sharedMemoryResource;
            $memoryNumber = preg_replace('/[^0-9]/', '', explode(' ', $memory)[2]);
            $this->resourcePool[$workerName][$memoryNumber] = [
                $sharedMemoryResource,
                $sharedMemoryKey
            ];
        }
    }

    /** Метод читает из ресурса разделяемой памяти.
     * @param resource $memoryResource - ресурс разделяемой памяти.
     * @param int $start - символ с которога начинать чтение из разделяемой памяти.
     * @param int $size - размер разделяемой памяти в kB.
     * @return string|null
     */
    public function read($memoryResource, int $start = 0, int $size = 0): ?string
    {
        if (SharedMemory::isResource($memoryResource)) {
            $read = shmop_read($memoryResource, $start, $size);
            return $read;
        }
        return null;
    }

    /** Метод читает из всех ресурсов и записывает в выходной массив.
     * @return array
     */
    public function readAllDataFromResourcePool(): array
    {
        foreach ($this->resourcePool as $workerName => $configations) {
            foreach ($configations as $key => $value) {
                $memoryResource = $value[0];
                $read = $this->read($memoryResource, 0, shmop_size($memoryResource));
                $data = unserialize($read);
                $this->output[$workerName][$key] = $data;
            }
        }

        return $this->output;
    }

    /** Метод записывает в указанный участок разделяемой памяти сериализованный массив.
     * @param resource $memoryResource - ресурс разделяемой памяти.
     * @param array $data - данные для записи.
     * @param int $offset - символ с которого начнется запись в участок разделяемой памяти.
     * @return int|null
     */
    public function write($memoryResource, array $data, int $offset = 0): ?int
    {
        if (SharedMemory::isResource($memoryResource)) {
            $serialize = serialize($data);
            $write = shmop_write($memoryResource, "{$serialize}", $offset);
            return $write;
        }
        return 0;
    }

    /** Метод удаляет участок разделяемой памяти по ресурсу
     * @param resource $memoryResource - ресурс разделяемой памяти
     * @return bool
     */
    public function delete($memoryResource): bool
    {
        if (SharedMemory::isResource($memoryResource)) {
            $delete = shmop_delete($memoryResource);
            return $delete;
        }
        return false;
    }

    /** Метод освобождает все ресурсы разделяемой памяти занятые во время выполнения
     *  и удаляет соответствующие записи в наборе ресурсов.
     * @return bool
     */
    public function deleteAllDataFromResourcePool(): bool
    {
        foreach ($this->resourcePool as $workerName => $configations) {
            foreach ($configations as $key => $value) {
                $memoryResource = $value[0];
                $memoryNumber = $value[1];
                $delete = $this->delete($memoryResource);
                if ($delete === true) {
                    unset($this->resourcePool[$key]);
                }
            }
        }

        return !empty($this->resourcePool);
    }

    /** Метод проверяет является ли аргумент ресурсом.
     * @param resource $resource - ресурс разделяемой памяти.
     * @return bool
     */
    public static function isResource($resource): bool
    {
        return is_resource($resource) ? true : false;
    }

    /** Метод считает сколько памяти занимает данный крипт.
     * @return int
     */
    public static function getMemoryUsage()
    {
        return memory_get_usage(true);
    }

    /** Метод возвращает набор ресурсов.
     * @return array
     */
    public function getResourcePool(): array
    {
        return $this->resourcePool;
    }

    /** Метод возвращает набор конфигурация для набора ресурсов
     * @return array
     */
    public function getCongirationsForResourcePool() : array
    {
        return $this->resourcePoolСonfirations;
    }

    /**
     * @param string|null $workerName - ключ в массиве $this->output, так же название файла воркера
     * @return array
     */
    public function getData(string $workerName = null) : array
    {
        if($workerName !== null && array_key_exists($workerName, $this->output)) {
            return $this->output[$workerName];
        }
        return $this->output;
    }
}