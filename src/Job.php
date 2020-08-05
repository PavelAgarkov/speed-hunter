<?php

namespace src;

use src\SharedMemory;

class Job
{
    private string $workerName;

    private int $processNumber;

    private int $sharedMemoryKey;

    private int $sharedMemorySize;

    private SharedMemory $SharedMemory;

    private $sharedMemoryResource = null;

    private ?string $readData;

    private string $type;

    public function __construct(array $argv, string $type) {

        $this->workerName       = (string)$argv[0];
        $this->processNumber    = (int)$argv[1];
        $this->sharedMemoryKey  = (int)$argv[2];
        $this->sharedMemorySize = (int)$argv[3];
        $this->SharedMemory     = new SharedMemory();
        $this->type             = $type;

    }

    public function restoreSharedMemoryResource(string $flag) : Job
    {
        $resource = $this->SharedMemory->open(
            $this->sharedMemoryKey,
            $flag,
            $this->sharedMemorySize
        );

        $this->sharedMemoryResource = $resource;
        return $this;
    }

    public function readFromSharedMemoryResource() : ?string
    {
        $read = $this->SharedMemory->read($this->sharedMemoryResource, 0, shmop_size($this->sharedMemoryResource) - 0);
        $this->readData = $read;
        return $this->readData;
    }

    public function writeIntoSharedMemoryResource(array $array) : ?int
    {
        $write = $this->SharedMemory->write(
            $this->sharedMemoryResource,
            $array
        );

        return $write;
    }

    public function handler(callable $function, string $read) : array
    {
        $array = $function($this, unserialize($read));
        return $array;
    }

    public function deleteDataFromSharedMemoryResource() : bool
    {
        return $this->SharedMemory->delete($this->sharedMemoryResource);
    }

    public function getSize() : int
    {
        return shmop_size($this->sharedMemoryResource);
    }

    public static function runJob(array $argv, string $type = 'array' , callable $function) : void
    {
        $Job = new Job($argv, $type);

        $Job->restoreSharedMemoryResource('w');

        $read = $Job->readFromSharedMemoryResource();

        $array = $Job->handler($function, $read);

        $Job->writeIntoSharedMemoryResource($array);
    }
}