<?php

namespace src;

use src\SharedMemory;


//Напрашивается выделение двух объектов :
//1. Объект для $workerName, $processNumber, $readData, $type, $serializeFlag(что-то вроде InputObject)
//2. Объект для $sharedMemoryKey, $sharedMemorySize, $SharedMemory, $sharedMemoryResource(что-то вроде SharedMemoryJobClient)

/** Класс заданий для реализации интерфейса работы в воркере
 * Class Job
 * @package src
 */
class Job
{
    const SERIALIZE_TRUE = 1;

    /**
     * @var string - имя зпущенного воркера
     */
    private string $workerName;

    /**
     * @var int - номер процесса по счету, переданный при запуске процесса, вызванного из основного процесса
     */
    private int $processNumber;

    /**
     * @var int - ключ разделяемой памяти, переданный при запуске процесса
     */
    private int $sharedMemoryKey;

    /**
     * @var int - размер разделяемой памяти, переданный при запуске процесса
     */
    private int $sharedMemorySize;

    /**
     * @var \src\SharedMemory - объект разделяемой памяти, для синхронизации с записанными данными
     */
    private SharedMemory $SharedMemory;

    /**
     * @var resource null - ресурс разделяемой памяти
     */
    private $sharedMemoryResource = null;

    /**
     * @var string|null - прочитанные данные из разделяемой памяти
     */
    private ?string $readData;

    /**
     * @var string - тип серилизованных данных до записи в разделяемую память из воркера
     */
    private string $type;

    /**
     * @var int
     */
    private int $serializeFlag;

    /**
     * Job constructor.
     * @param array $argv - аргументы из вызываемого процесса
     * @param string $type - тип данных до сериализации и записи из воркера в раздеяемую память
     */
    public function __construct(array $argv, string $type)
    {
        $this->workerName = (string)$argv[0];
        $this->processNumber = (int)$argv[1];
        $this->sharedMemoryKey = (int)$argv[2];
        $this->sharedMemorySize = (int)$argv[3];
        $this->serializeFlag = (int)$argv[4];

        $this->SharedMemory = new SharedMemory();
        $this->type = $type;
    }

    /** Метод восстанавливает ресурс памяти по указанному флагу
     * @param string $flag - флаг открытия памяти
     * @return $this
     */
    public function restoreSharedMemoryResource(string $flag): Job
    {
        $resource = $this->SharedMemory->open(
            $this->sharedMemoryKey,
            $flag,
            $this->sharedMemorySize
        );

        $this->sharedMemoryResource = $resource;
        return $this;
    }

    /** Метод для чтения данных из разделяемой памяти для данного воркера
     * @return string|null
     */
    public function readFromSharedMemoryResource(): ?string
    {
        $read = $this->SharedMemory->read(
            $this->sharedMemoryResource,
            0,
            shmop_size($this->sharedMemoryResource) - 0
        );
        $this->readData = $read;

        return $this->readData;
    }

    /** Метод для записи в участок разделяемой памяти переданных данных
     * @param array $array - данные
     * @return int|null
     */
    public function writeIntoSharedMemoryResource(array $array): ?int
    {
        $write = $this->SharedMemory->write(
            $this->sharedMemoryResource,
            $array
        );

        return $write;
    }

    /** Метод вызывает передаваемое замыкание
     * @param callable $function - анонимная функция для выполнения внутри класса
     * @param string $read - прочитанные данные из памяти
     * @return array
     */
    public function handler(callable $function, ?string $read): ?array
    {
        if ($read != "") {
            $unserialize = unserialize($read);
            if ($unserialize === false) {
                $unserialize = null;
            }
        } else {
            $unserialize = null;
        };

        $array = $function($this, $unserialize);

        return $array;
    }

    /** Метод измеряем затраченную память на скрипт
     * @return int
     */
    public function getSize(): int
    {
        return shmop_size($this->sharedMemoryResource);
    }

    /** Интерфейсный метод для запуска Job
     * @param array $argv - stdin
     * @param string $type - тип данных для сериализации
     * @param callable $function - замыкание для выполнения
     */
    public static function runJob(array $argv, callable $function): void
    {
        $Job = new Job($argv, 'array');

        $Job->restoreSharedMemoryResource('w');

        if ($Job->serializeFlag == static::SERIALIZE_TRUE) {
            $read = $Job->readFromSharedMemoryResource();
        } else {
            $read = "";
        }

        $array = $Job->handler($function, $read);

        $Job->writeIntoSharedMemoryResource($array);
    }

    /**
     * @param array $argv
     * @param callable $function
     */
    public static function runSingleAsyncJob(array $argv, callable $function): void
    {
        $Job = new Job($argv, 'array');

        $Job->restoreSharedMemoryResource('w');

        if ($Job->serializeFlag == static::SERIALIZE_TRUE) {
            $read = $Job->readFromSharedMemoryResource();
        } else {
            $read = "";
        }

        $Job->handler($function, $read);

        $Job->SharedMemory->delete($Job->sharedMemoryResource);
    }
}