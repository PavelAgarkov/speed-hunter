<?php

namespace src\client\settings\value_object;

use MongoDB\Driver\ClientEncryption;
use src\client\Client;
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

        parent::__construct(
            $settings["phpPath"],
            $settings["jobName"],
            $settings["shSizeForOneJob"],
            1
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