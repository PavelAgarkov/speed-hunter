<?php

namespace src\client\settings\settings_validator;

use src\client\settings\settings_validator\BaseValidatorInterface;
use src\client\settings\settings_validator\BaseValidator;
use src\client\settings\value_object\Settings;

/**
 * Class SingleProcessSettingsValidator
 * @package src\client\settings\settings_validator
 */
class SingleProcessSettingsValidator extends BaseValidator implements BaseValidatorInterface
{
    /**
     * @var array|\string[][]
     */
    protected array $rights =
        [
            "data" => [
                "type"      => "array",
                "required"  => "optional",
            ],

            "shSizeForOneJob" => [
                "type"      => "integer",
                "required"  => "optional",
                "depend"    => "data"
            ]
        ];

    /**
     * SingleProcessSettingsValidator constructor.
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