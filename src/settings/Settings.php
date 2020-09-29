<?php

namespace src\settings;

/**
 * Class Settings
 * @package src\settings
 */
abstract class Settings implements SettingsInterface
{
    /**
     * @var array
     */
    protected array $settingsObjects;

    /**
     * Settings constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getSettingsObjects(): array
    {
        return $this->settingsObjects;
    }
}