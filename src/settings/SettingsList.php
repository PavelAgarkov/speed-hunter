<?php

namespace src\settings;

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
     * @param Settings ...$settings
     */
    public function __construct(Settings ...$settings)
    {
        foreach ($settings as $key => $settingsObject) {
            $this->list[$settingsObject->getJobName()] = $settingsObject;
            $this->count++;
        }
    }

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
}