<?php

namespace src\routine\job;

use src\routine\job\value_object\LaunchedJob;
use src\routine\job\value_object\SharedMemoryJob;
use src\shared_memory\SharedMemoryManager;

/** Класс заданий для реализации интерфейса работы в воркере
 * Class Job
 * @package src
 */
class Job
{
    private const SERIALIZE_TRUE = 1;

    public const FULL_COMMAND = 1;

    /**
     * @var SharedMemoryJob|null
     */
    private ?SharedMemoryJob $sharedMemoryJob;

    /**
     * @var LaunchedJob
     */
    private LaunchedJob $launchedJob;

    /**
     * @var int
     */
    private int $fullJob;

    private string $type;

    /**
     * @var array|array[]
     */
    private array $output = [
        "read"      => [],
        "fromJob"    => []
    ];

    /**
     * Job constructor.
     * @param array $argv - аргументы из вызываемого процесса
     */
    public function __construct(array $argv)
    {
        $this->type = $argv[3];
        $this->fullJob = $argv[2];
        if($this->fullJob === static::FULL_COMMAND) {

            $this->launchedJob = new LaunchedJob(
                array(
                    "jobName" => (string)$argv[1],
                    "processNumber" => (int)$argv[4],
                    "serializeFlag" => (int)$argv[7]
                )
            );

            $this->sharedMemoryJob = new SharedMemoryJob(
                array(
                    "sharedMemoryKey" => (int)$argv[5],
                    "sharedMemorySize" => (int)$argv[6],
                    "flagShOpen" => "w"
                )
            );

        } else {

            $this->launchedJob = new LaunchedJob(
                array(
                    "jobName" => (string)$argv[1],
                    "processNumber" => (int)$argv[4],
                    "serializeFlag" => 0
                )
            );
            $this->sharedMemoryJob = null;

        }
    }

    /** Метод вызывает передаваемое замыкание
     * @param string|null $read - прочитанные данные из памяти
     * @return array
     */
    private function unserializer(?string $read): ?array
    {
        if ($read != "") {
            $unserialize = unserialize($read);
            if ($unserialize === false) {
                $unserialize = null;
            }
        } else {
            $unserialize = null;
        };

        return $unserialize;
    }

    public function runJob(): void
    {
        $this->synchroniseRead();

        $Resolver = new JobSharedMemoryResolver($this, $this->output);
        if (!$Resolver->check()) {
            $resolveSettings = $Resolver->reload();
            $Resolver->resolveSharedMemoryJob($resolveSettings);
        }

        SharedMemoryManager::writeIntoSh(
            $this->sharedMemoryJob->getSharedMemory(),
            $this->sharedMemoryJob->getSharedMemoryResource(),
            $this->output
        );
    }

    public function runSingleAsyncJob(): void
    {
        SharedMemoryManager::deleteSh(
            $this->sharedMemoryJob->getSharedMemory(),
            $this->sharedMemoryJob->getSharedMemoryResource()
        );
    }

    /**
     * @return SharedMemoryJob
     */
    public function getSharedMemoryJob(): ?SharedMemoryJob
    {
        return $this->sharedMemoryJob;
    }

    /**
     * @return LaunchedJob
     */
    public function getLaunchedJob(): LaunchedJob
    {
        return $this->launchedJob;
    }

    /**
     * @return array
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * @param string $key
     * @param array $output
     */
    public function insertInOutput(string $key, array $output): void
    {
        $this->output[$key] = $output;
    }

    public function synchroniseRead(): void
    {
        $read = $this->launchedJob->getSerializeFlag() == static::SERIALIZE_TRUE
            ? $this->sharedMemoryJob->getReadData() : "";

        $this->output["read"] = $this->unserializer($read);
    }

    /**
     * @return int
     */
    public function getFullJob(): int
    {
        return $this->fullJob;
    }

    /**
     * @return bool
     */
    public function isSingleAsync(): bool
    {
        if($this->type === "singleAsync") {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        if($this->type === "multiple") {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isMultipleAsync(): bool
    {
        if($this->type === "multipleAsync") {
            return true;
        }
        return false;
    }
}