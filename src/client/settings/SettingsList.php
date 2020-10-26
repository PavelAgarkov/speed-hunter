<?php

namespace src\client\settings;

use src\client\planned_routines\BaseRoutine;
use src\client\settings\value_object\MultipleProcessesSettings;
use src\client\settings\value_object\Settings;

/**
 * Class SettingsList
 * @package src\settings
 */
final class SettingsList
{
    /**
     * @var array
     */
    private array $list = array();

    /**
     * @var int
     */
    private int $count = 0;

    /**
     * SettingsList constructor.
     */
    public function __construct(){}

    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * @param string $name
     * @return Settings
     */
    public function getSettingsObject(string $name): Settings
    {
        return $this->list[$name];
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return Settings
     */
    public function getFirst(): Settings
    {
        return current($this->list);
    }

    /**
     * @return Settings
     */
    public function getLast(): Settings
    {
        return end($this->list);
    }

    public function set(string $key,
                        MultipleProcessesSettings $settings): self
    {
        $this->list[$key] = $settings;
        $this->count++;

        return $this;
    }
}