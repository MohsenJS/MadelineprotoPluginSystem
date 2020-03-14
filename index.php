<?php

declare(strict_types=1);

use MohsenJS\Tools;
use MohsenJS\Config;
use MohsenJS\EventHandler;
use danog\MadelineProto\API;

require __DIR__ . '/vendor/autoload.php';

Tools::checkDataPath();
$MadelineProto = new API(Config::DATA_PATH . Config::SESSION_NAME, Config::SETTINGS);
$MadelineProto->startAndLoop(EventHandler::class);
