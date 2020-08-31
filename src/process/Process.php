<?php

namespace src\process;

use src\ResourcePool;

abstract class Process
{
    protected ResourcePool $ResourcePool;

    public function __construct(ResourcePool $pool)
    {
        $this->ResourcePool = $pool;
    }

    public function getResourcePool() : ResourcePool
    {
        return $this->ResourcePool;
    }



}