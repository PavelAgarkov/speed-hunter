<?php

namespace src\job;

use src\job\value_object\LaunchedJob;
use src\job\value_object\SharedMemoryJob;
use src\shared_memory\SharedMemoryManager;

/** Класс заданий для реализации интерфейса работы в воркере
 * Class Job
 * @package src
 */
class Job
{
    const SERIALIZE_TRUE = 1;

    /**
     * @var SharedMemoryJob
     */
    private SharedMemoryJob $sharedMemoryJob;

    /**
     * @var LaunchedJob
     */
    private LaunchedJob $launchedJob;

    /**
     * @var string - тип серилизованных данных до записи в разделяемую память из воркера
     */
    private string $type;


    /**
     * Job constructor.
     * @param array $argv - аргументы из вызываемого процесса
     * @param string $type - тип данных до сериализации и записи из воркера в раздеяемую память
     */
    private function __construct(array $argv, string $type)
    {
        $this->launchedJob = new LaunchedJob(
            array(
                "jobName" => (string)$argv[0],
                "processNumber" => (int)$argv[1],
                "serializeFlag" => (int)$argv[4]
            )
        );

        $this->sharedMemoryJob = new SharedMemoryJob(
            array(
                "sharedMemoryKey" => (int)$argv[2],
                "sharedMemorySize" => (int)$argv[3],
                "flagShOpen" => "w"
            )
        );

        $this->type = $type;
    }

    /** Метод вызывает передаваемое замыкание
     * @param callable $function - анонимная функция для выполнения внутри класса
     * @param string|null $read - прочитанные данные из памяти
     * @return array
     */
    private function handler(callable $function, ?string $read): ?array
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

    /** Интерфейсный метод для запуска Job
     * @param array $argv - stdin
     * @param callable $function - замыкание для выполнения
     */
    public static function runJob(array $argv, callable $function): void
    {
        $Job = new Job($argv, 'array');
        $read = $Job->launchedJob->getSerializeFlag() == static::SERIALIZE_TRUE
            ? $Job->sharedMemoryJob->getReadData() : "";

        $data = $Job->handler($function, $read);

        SharedMemoryManager::writeIntoSh(
            $Job->sharedMemoryJob->getSharedMemory(),
            $Job->sharedMemoryJob->getSharedMemoryResource(),
            $data
        );
    }

    /**
     * @param array $argv
     * @param callable $function
     */
    public static function runSingleAsyncJob(array $argv, callable $function): void
    {
        $Job = new Job($argv, 'array');
        $read = $Job->launchedJob->getSerializeFlag() == static::SERIALIZE_TRUE
            ? $Job->sharedMemoryJob->getReadData() : "";

        $Job->handler($function, $read);

        SharedMemoryManager::deleteSh(
            $Job->sharedMemoryJob->getSharedMemory(),
            $Job->sharedMemoryJob->getSharedMemoryResource()
        );
    }
}