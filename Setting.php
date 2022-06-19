<?php

/**
 * MadelineProto settings.
 *
 * @see https://docs.madelineproto.xyz/docs/SETTINGS.html
 */

declare(strict_types=1);

use OxMohsen\Config;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Templates;
use danog\MadelineProto\Settings\Database\Mysql;

$settings = new Settings();
$settings->getLogger()->setLevel(Config::LOGGER_LEVEL);
$settings->setDb((new Mysql())
    ->setUri(Config::DB_HOST)
    ->setDatabase(Config::DB_NAME)
    ->setUsername(Config::DB_USERNAME)
    ->setPassword(Config::DB_PASSWORD));

$settings->setTemplates((new Templates())
    ->setHtmlTemplate(base64_decode(file_get_contents(__DIR__.'/web-template'))));

$settings->setAppInfo((new AppInfo())
    // if you want to use your own api_id and api_hash manually, you can set them here
    // ->setApiId(4)
    // ->setApiHash('014b35b6184100b085b0d0572f9b5103')
    ->setDeviceModel('OxMohsen')
    ->setAppVersion('2.0')
    ->setLangCode('en')
    ->setSystemVersion('v2.0.0'));
