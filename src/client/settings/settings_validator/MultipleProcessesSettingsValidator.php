<?php

namespace src\client\settings\settings_validator;

use src\client\settings\settings_validator\BaseValidatorInterface;
use src\client\settings\settings_validator\BaseValidator;
use src\client\settings\value_object\Settings;

/**
 * Class MultipleProcessesSettingsValidator
 * @package src\client\settings\settings_validator
 */
class MultipleProcessesSettingsValidator extends BaseValidator implements BaseValidatorInterface
{
    /**
     * @var array
     */
    protected array $rights =
        [
            "numberJobs" => [
                "type"      => "integer",
                "required"  => "required",
            ],

            "dataPartitioning" => [
                "flagPartitioning" => [
                    "type"      => "integer",
                    "required"  => "optional",
                    "depend"    => "dataToPartitioning"
                ],
                "dataToPartitioning" => [
                    "type"      => "array",
                    "required"  => "optional",
                ]
            ],

            "shSizeForOneJob" => [
                "type"      => "integer",
                "required"  => "optional",
                "depend"    => "dataPartitioning"
            ]
        ];

    /**
     * MultipleProcessesSettingsValidator constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        parent::__construct($settings);
    }

    public function validate(): void
    {
        parent::baseValidate($this->rights);
    }
}