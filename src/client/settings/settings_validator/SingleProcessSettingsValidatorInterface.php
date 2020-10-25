<?php

namespace src\client\settings\settings_validator;

use src\client\settings\settings_validator\BaseValidatorInterface;

interface SingleProcessSettingsValidatorInterface extends BaseValidatorInterface
{
    public const ALLOWED_SINGLE_SETTINGS = [
        "data" => [
            "type"      => ["array"],
            "required"  => "optional",
        ],

        "shSizeForOneJob" => [
            "type"      => ["integer"],
            "required"  => "optional",
        ]
    ];
}
