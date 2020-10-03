<?php

namespace src\job\value_object;

use src\shared_memory\SharedMemory;
use src\shared_memory\SharedMemoryManager;

/**
 * Class SharedMemoryJob
 * @package src\job\value_object
 */
class SharedMemoryJob
{
    /**
     * @var int - ключ разделяемой памяти, переданный при запуске процесса
     */
    private int $sharedMemoryKey;

    /**
     * @var int - размер разделяемой памяти, переданный при запуске процесса
     */
    private int $sharedMemorySize;

    /**
     * @var SharedMemory - объект разделяемой памяти, для синхронизации с записанными данными
     */
    private SharedMemory $SharedMemory;

    /**
     * @var resource - ресурс разделяемой памяти
     */
    private $sharedMemoryResource;

    /**
     * @var string|null - прочитанные данные из разделяемой памяти
     */
    private ?string $readData;

    public function __construct(array $inputData)
    {
        $this->SharedMemory = new SharedMemory();
        $this->sharedMemoryKey = $inputData["sharedMemoryKey"];
        $this->sharedMemorySize = $inputData["sharedMemorySize"];

        $this->sharedMemoryResource = SharedMemoryManager::openShResource(
            $this->SharedMemory,
            array(
                "sharedMemoryKey" => $this->sharedMemoryKey,
                "openFlag" => $inputData["flagShOpen"],
                "shSize" => $this->sharedMemorySize
            )
        );

        $this->readData = SharedMemoryManager::readShResource(
            $this->SharedMemory,
            array(
                "sharedMemoryResource" => $this->sharedMemoryResource,
                "start" => 0,
                "size" => shmop_size($this->sharedMemoryResource) - 0
            )
        );
    }

    /**
     * @return int
     */
    public function getSharedMemoryKey(): int
    {
        return $this->sharedMemoryKey;
    }

    /**
     * @return int
     */
    public function getSharedMemorySize(): int
    {
        return $this->sharedMemorySize;
    }

    /**
     * @return SharedMemory
     */
    public function getSharedMemory(): SharedMemory
    {
        return $this->SharedMemory;
    }

    /**
     * @return resource
     */
    public function getSharedMemoryResource()
    {
        return $this->sharedMemoryResource;
    }

    /**
     * @return string|null
     */
    public function getReadData(): ?string
    {
        return $this->readData;
    }
}