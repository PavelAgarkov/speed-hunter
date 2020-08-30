<?php

namespace src\process;

use src\data_manager\DataManagerForWorkers;
use src\settings\Settings;

class ProcessManager
{
    /**
     * @var array - массив объектов DataManager для каждого набора WorkerProcess
     */
    protected array $dataManagerForWorkers;

    protected Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getSettings() : Settings
    {
        return $this->settings;
    }

    public function getDataManagerForWorkers() : array
    {
        return $this->dataManagerForWorkers;
    }

    public function setDataManagerForWorkers(string $key, DataManagerForWorkers $dataManagerForWorkers) : void
    {
        $this->dataManagerForWorkers[$key] = $dataManagerForWorkers;
    }

}