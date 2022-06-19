<?php

declare(strict_types=1);

namespace OxMohsen;

use danog\MadelineProto\Logger;

final class Config
{
    /**
     * array of user-id of admins.
     * e.g. 123456789.
     *
     * @var int[]
     */
    public const ADMINS = [];

    /**
     * database host uri.
     */
    public const DB_HOST = '127.0.0.1:3306';

    /**
     * database name.
     */
    public const DB_NAME = 'MadelineProto';

    /**
     * database username.
     */
    public const DB_USERNAME = 'root';

    /**
     * database password.
     */
    public const DB_PASSWORD = '';

    /**
     * logger type.
     *
     * @see https://docs.madelineproto.xyz/PHP/danog/MadelineProto/Settings/Logger.html
     */
    public const LOGGER_LEVEL = Logger::LEVEL_FATAL;

    /**
     * Session file name.
     */
    public const SESSION_NAME = 'OxMohsen.madeline';

    /**
     * Data folder path.
     *
     * This path is used for saving session file.
     */
    public const DATA_PATH = __DIR__.'/Data/';

    /**
     * Plugins folder path.
     *
     * Please change this if you know what you are doing.
     */
    public const PLUGIN_PATH = __DIR__.'/Plugins/';
}
