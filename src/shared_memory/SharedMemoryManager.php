<?php

namespace src\shared_memory;

/**
 * Class SharedMemoryManager
 * @package src
 */
class SharedMemoryManager
{
    /**
     * @param SharedMemory $sharedMemory
     * @param array $shSettings
     * @return resource
     */
    public static function openShResource
    (
        SharedMemory $sharedMemory,
        array $shSettings
    )
    {
        return $sharedMemory->open(
            $shSettings["sharedMemoryKey"],
            $shSettings["openFlag"],
            $shSettings["shSize"]
        );
    }

    /**
     * @param SharedMemory $sharedMemory
     * @param array $shSettings
     * @return string|null
     */
    public static function readShResource
    (
        SharedMemory $sharedMemory,
        array $shSettings
    )
    {
        return $sharedMemory->read(
            $shSettings["sharedMemoryResource"],
            $shSettings["start"],
            $shSettings["size"]
        );
    }

    /**
     * @param SharedMemory $sharedMemory
     * @param $resource
     * @param array $data
     * @return int|null
     */
    public static function writeIntoSh
    (
        SharedMemory $sharedMemory,
        $resource,
        array $data
    )
    {
        return $sharedMemory->write(
            $resource,
            $data
        );
    }

    /**
     * @param SharedMemory $sharedMemory
     * @param $resource
     * @return int|null
     */
    public static function getShSize
    (
        SharedMemory $sharedMemory,
        $resource
    ): ?int
    {
        return $sharedMemory->getSize($resource);
    }

    /**
     * @param SharedMemory $sharedMemory
     * @param $resource
     * @return bool
     */
    public static function deleteSh
    (
        SharedMemory $sharedMemory,
        $resource
    )
    {
        return $sharedMemory->delete($resource);
    }

}