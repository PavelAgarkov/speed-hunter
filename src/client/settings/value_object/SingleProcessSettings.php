<?php

namespace src\client\settings\value_object;

use src\client\settings\value_object\Settings;

/**
 * Class SingleProcessSettings
 * @package src\settings
 */
final class SingleProcessSettings extends Settings
{
    /**
     * @var array
     */
    private array $data = array();

    /**
     * SingleProcessSettings constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        parent::__construct(
            $settings["phpPath"],
            $settings["jobName"],
            $settings["shSizeForOneJob"],
            1
        );

        $this->data = $settings["data"];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}