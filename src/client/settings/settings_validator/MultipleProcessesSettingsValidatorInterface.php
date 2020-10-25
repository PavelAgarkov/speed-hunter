<?php

namespace src\client\settings\settings_validator;

interface MultipleProcessesSettingsValidatorInterface extends BaseValidatorInterface
{
    public const ALLOWED_MULTIPLE_SETTINGS = [
        "numberJobs" => [
            "type"      => ["integer"],
            "required"  => "required",
        ],

        "dataPartitioning" => [
            "flagPartitioning" => [
                "type"      => ["integer"],
                "required"  => "optional",
            ],
            "dataToPartitioning" => [
                "type"      => ["array"],
                "required"  => "optional",
            ]
        ],

        "shSizeForOneJob" => [
            "type"      => ["integer"],
            "required"  => "optional",
        ]
    ];
}