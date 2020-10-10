<?php

namespace src\client\process;

use src\client\ResourcePool;

/**
 * Class Process
 * @package src\process
 */
abstract class Process
{
    /**
     * @var ResourcePool
     */
    protected ResourcePool $ResourcePool;

    /**
     * Process constructor.
     * @param ResourcePool $pool
     */
    public function __construct(ResourcePool $pool)
    {
        $this->ResourcePool = $pool;
    }

    /**
     * @return ResourcePool
     */
    public function getResourcePool(): ResourcePool
    {
        return $this->ResourcePool;
    }
}