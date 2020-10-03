<?php

namespace src\shared_memory;

/** Класс управления разделяемой памятью unix для параллельной работы процессов.
 * Class SharedMemory
 * @package src
 */
class SharedMemory
{
    /**
     * @var array - массив, в который записываются данные из каждого ресурса разделяемой памяти.
     */
    private array $output;

    public function __construct()
    {
    }

    /** Метод создающий участок разделяемой памяти.
     * @param int $memoryKey - ключ разделяемой памяти.
     * @param string $openFlag - флаг для открытия ресурса с определенными набором возможностей.
     * @param int $size - объем памяти в kB для одного учатка.
     * @return resource
     */
    public function open(
        int $memoryKey,
        string $openFlag = "n",
        int $size = 0
    ) {
        $sharedMemoryResource = shmop_open($memoryKey, $openFlag, 0755, $size);
        return $sharedMemoryResource;
    }

    /** Метод читает из ресурса разделяемой памяти.
     * @param resource $memoryResource - ресурс разделяемой памяти.
     * @param int $start - символ с которога начинать чтение из разделяемой памяти.
     * @param int $size - размер разделяемой памяти в kB.
     * @return string|null
     */
    public function read(
        $memoryResource,
        int $start = 0,
        int $size = 0
    ): ?string {
        if (SharedMemory::isResource($memoryResource)) {
            $read = shmop_read($memoryResource, $start, $size);
            return $read;
        }
        return null;
    }

    /** Метод записывает в указанный участок разделяемой памяти сериализованный массив.
     * @param resource $memoryResource - ресурс разделяемой памяти.
     * @param array $data - данные для записи.
     * @param int $offset - символ с которого начнется запись в участок разделяемой памяти.
     * @return int|null
     */
    public function write($memoryResource, array $data): ?int
    {
        if (SharedMemory::isResource($memoryResource)) {
            $serialize = serialize($data);
            $write = shmop_write($memoryResource, $serialize, 0);
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

    /**
     * @param string|null $workerName - ключ в массиве $this->output, так же название файла воркера
     * @return array
     */
    public function getData(string $workerName = null): array
    {
        if ($workerName !== null && array_key_exists($workerName, $this->output)) {
            return $this->output[$workerName];
        }
        return $this->output;
    }

    public function getSize($resourceId): int
    {
        return is_resource($resourceId) ? shmop_size($resourceId) : 0;
    }

    /**
     * @param string $workerName
     * @param string $key
     * @param array|null $data
     * @param array $value
     */
    public function setOutputElementByKey(
        string $workerName,
        string $key,
        ?array $data,
        array $value
    ): void {
        if ($data === null) {
            throw new \RuntimeException(
                "Shared memory node id ${value[1]} in process name ${workerName} less than necessary!"
            );
        }
        $this->output[$workerName][$key] = $data;
    }
}