<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/vendor/autoload.php';

$validation = \src\client\settings\settings_validator\SingleProcessSettingsValidator::BASE_VALIDATE_VALUE;