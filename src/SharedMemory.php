<?php

namespace src;

class SharedMemory
{
    private array $resoucePool = [];

    private int $countResources;

    private array $output;

    public function __construct()
    {
    }

    public function createResourcePool(int $countResources = 0, int $memorySize = 0)
    {
        $this->countResources = $countResources;

        while (count($this->resoucePool) < $this->countResources) {
            //  ключ разделяемой памяти
            $sharedMemoryKey = rand(100, 1000000);
            //флаг "n" говорит, что создается новый участок общей памяти и возвращает false если участок с таким ключом уже есть
            // permission 0755
            $sharedMemoryResource = $this->open($sharedMemoryKey, "n", $memorySize);

            // если разделяемая память по данному ключу занята, то делаем новый ключ, иначе записываем в пул памяти
            $this->addInResourcePool($sharedMemoryResource, $sharedMemoryKey);
        }
    }

    public function open(int $memoryKey, string $openFlag = "n", $size = 0)
    {
        $sharedMemoryResource = shmop_open($memoryKey, $openFlag, 0755, $size);
        return $sharedMemoryResource;
    }

    public function addInResourcePool($sharedMemoryResource, $sharedMemoryKey): void
    {
        if ($sharedMemoryResource === false) {
            $sharedMemoryKey = rand(100, 1000000);
        } else {
            $memory = (string)$sharedMemoryResource;
            $memoryNumber = preg_replace('/[^0-9]/', '', explode(' ', $memory)[2]);
            $this->resoucePool[][$memoryNumber] = [
                $sharedMemoryResource,
                $sharedMemoryKey
            ];
        }
    }

    public function read($memoryResource, $start = 0, $size = 0): ?string
    {
        if (SharedMemory::isResource($memoryResource)) {
            $read = shmop_read($memoryResource, $start, $size);
            return $read;
        }
        return null;
    }

    public function readAllDataFromResourcePool(): array
    {
        foreach (range(0, $this->countResources - 1) as $key => $item) {
            $memoryResource = current($this->resoucePool[$key])[0];
            $read = $this->read($memoryResource, 0, shmop_size($memoryResource));
            $data = unserialize($read);
            $this->output[] = $data;
        }

        return $this->output;
    }

    public function write($memoryResource, array $data, int $offset = 0): ?int
    {
        if (SharedMemory::isResource($memoryResource)) {
            $serialize = serialize($data);
            $write = shmop_write($memoryResource, "{$serialize}", $offset);
            return $write;
        }
    }

    public function delete($memoryResource): bool
    {
        if (SharedMemory::isResource($memoryResource)) {
            $delete = shmop_delete($memoryResource);
            return $delete;
        }
    }

    public function deleteAllDataFromResourcePool(): bool
    {
        foreach (range(0, $this->countResources - 1) as $key => $item) {
            $memoryResource = current($this->resoucePool[$key])[0];
            $memoryNumber = current($this->resoucePool[$key])[1];
            $delete = $this->delete($memoryResource);
            if ($delete === true) {
                unset($this->resoucePool[$key]);
            }
        }

        return !empty($this->resoucePool);
    }

    public static function isResource($resource): bool
    {
        return is_resource($resource) ? true : false;
    }

    public static function getMemoryUsage()
    {
        return memory_get_usage(true);
    }

    public function getResourcePool(): array
    {
        return $this->resoucePool;
    }
}