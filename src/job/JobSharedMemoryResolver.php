<?php

namespace src\job;

use http\Exception\RuntimeException;
use src\shared_memory\SharedMemoryManager;

class JobSharedMemoryResolver
{
    private Job $job;

    private array $data;

    private string $serializeData;

    public int $serializeDataSize;

    public function __construct(Job $job, array $data)
    {
        $this->job = $job;
        $this->data = $data;
        $this->serializeData = serialize($data);
    }

    public function check(): bool
    {
        $this->serializeDataSize = strlen($this->serializeData);
        if($this->serializeDataSize > $this->job->getSharedMemoryJob()->getSharedMemorySize()) {
            return false;
        }
        return true;
    }

    public function reload(): array
    {
        $shJob = $this->job->getSharedMemoryJob();
        $sh = $this->job->getSharedMemoryJob()->getSharedMemory();
        $shKey = $shJob->getSharedMemoryKey();
        $shSize = $shJob->getSharedMemorySize();
        $shResource = $shJob->getSharedMemoryResource();

        $delete = SharedMemoryManager::deleteSh(
            $sh,
            $shResource
        );
        if(!$delete) {
            throw new \RuntimeException("Не удалось удалить sh {$shKey} с ресурсом {$shResource}");
        }

        $this->roundUp($shSize);

        $open = SharedMemoryManager::openShResource(
            $sh,
            array(
                "sharedMemoryKey" => $shKey,
                "openFlag" => "n",
                "shSize" => $shSize
            )
        );
        if(!$open) {
            throw new \RuntimeException("Не удалось открыть sh {$shKey} с ресурсом {$shResource}");
        }

        return array(
            "shSize" => $shSize,
            "shResource" => $open
        );
    }

    public function roundUp(string &$shSize): void
    {
        $startSize = (float)$this->serializeDataSize;
        $roundSize = round($startSize, 0);
        $shSize = (int)$roundSize;
    }

    public function resolveSharedMemoryJob(array $resolveSettings): void
    {
        $shJob = $this->job->getSharedMemoryJob();
        $shJob->setSharedMemoryResource($resolveSettings["shResource"]);
        $shJob->setSharedMemorySize($resolveSettings["shSize"]);
    }
}