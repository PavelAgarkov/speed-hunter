<?php

namespace src\process;

use src\ResourcePool;

class AsyncProcess extends Process
{
    public function __construct(ResourcePool $pool)
    {
        parent::__construct($pool);
    }

    public function singleProcessOpen(): void
    {
        $settings = $this->ResourcePool->getSettingsForSingleProcess();

        $name = $settings['jobName'];
        $shResources = $this->ResourcePool->getResourceByJobName($name);

        $resourceKey = array_key_first($shResources);
        $numberMemory = current($shResources)[1];
        $size = $this->ResourcePool->getSharedMemory()->getSize(current($shResources)[0]);

        proc_close(proc_open("php {$name}.php {$resourceKey} {$numberMemory} {$size} 1 --foo=1 &", array(), $foo));
    }
}