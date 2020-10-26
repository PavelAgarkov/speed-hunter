<?php

namespace src\client\settings\value_object;

use src\client\Client;
use src\client\settings\settings_validator\BaseValidatorInterface;
use src\client\settings\settings_validator\SingleProcessSettingsValidator;
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
        if(isset($settings["data"])) {
            $this->data = $settings["data"];
        }

        if(!isset($settings["shSizeForOneJob"])) {
            $settings["shSizeForOneJob"] = Client::weighData([]);
        }

        $validator = new SingleProcessSettingsValidator($this);

        parent::__construct(
            $settings["phpPath"] ?? null,
            $settings["jobName"] ?? null,
            $settings["shSizeForOneJob"] ?? null,
            1,
            $validator
        );
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}