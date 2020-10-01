<?php

namespace src\process;

use src\ResourcePool;

/**
 * Class AsyncProcess
 * @package src\process
 */
class AsyncProcess extends Process
{
    /**
     * AsyncProcess constructor.
     * @param ResourcePool $pool
     */
    public function __construct(ResourcePool $pool)
    {
        parent::__construct($pool);
    }

    /**
     *
     */
    public function singleProcessOpen(): void
    {
        $workerProcess = current($this->ResourcePool->getPoolOfWorkers());
        $name = $workerProcess->getWorkerName();
        $phpPath = $workerProcess->getPhpPath();

        $shResources = $this->ResourcePool->getResourceByJobName($name);

        $resourceKey = array_key_first($shResources);
        $numberMemory = current($shResources)[1];
        $size = $this->ResourcePool->getSharedMemory()->getSize(current($shResources)[0]);

        proc_close(
            proc_open(
                "{$phpPath} {$name}.php {$resourceKey} {$numberMemory} {$size} 1 --foo=1 &",
                array(),
                $foo
            )
        );
    }

    /**
     *
     */
    public function multipleProcessesOpen(): void
    {
        $workerProcess = $this->ResourcePool->getPoolOfWorkers();
        foreach ($this->ResourcePool->getResourcePool() as $workerName => $configurations) {
            $settings = $workerProcess[$workerName];

            foreach ($configurations as $resourceKey => $value) {
                $name = $settings->getWorkerName();
                $phpPath = $settings->getPhpPath();
                $shResources = $this->ResourcePool->getResourceByJobName($name);
                $numberMemoryKey = $value[1];
                $size = $this->ResourcePool->getSharedMemory()->getSize(current($shResources)[0]);

                proc_close(
                    proc_open(
                        "{$phpPath} {$name}.php {$resourceKey} {$numberMemoryKey} {$size} 1 --foo=1 &",
                        array(),
                        $foo
                    )
                );
            }
        }
    }
}