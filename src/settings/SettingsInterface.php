<?php

namespace src\settings;

/**
 * Interface SettingsInterface
 * @package src\settings
 */
interface SettingsInterface
{
    /**
     * @return array
     */
    public function getSettingsObjects(): array;
}