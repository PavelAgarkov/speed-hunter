<?php

namespace src\settings;

abstract class Settings implements SettingsInterface
{
    protected array $settingsObjects;

    public function __construct()
    {

    }

    public function getSettingsObjects() : array
    {
        return $this->settingsObjects;
    }
}