<?php

declare(strict_types=1);

use OxMohsen\Tools;
use OxMohsen\Config;
use OxMohsen\PluginEventHandler;

/**
 * load library's.
 */
require __DIR__.'/vendor/autoload.php';

/**
 * load madelineproto $setting.
 */
require __DIR__.'/Setting.php';

Tools::checkDataPath();
PluginEventHandler::startAndLoop(Config::DATA_PATH.Config::SESSION_NAME, $settings);
