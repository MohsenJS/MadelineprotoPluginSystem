<?php

declare(strict_types=1);

namespace MohsenJS;

final class Config
{
    /**
     * the user id of admins.
     */
    public const ADMINS = [];

    /**
     * MadelineProto settings.
     *
     * @see https://docs.madelineproto.xyz/docs/SETTINGS.html
     */
    public const SETTINGS = [
        'serialization' => [
            'serialization_interval' => 30,
        ],
    ];

    /**
     * Session file name.
     */
    public const SESSION_NAME = 'MohsenJS.madeline';

    /**
     * Data folder path.
     *
     * This path is used for saving some files.
     */
    public const DATA_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR;

    /**
     * Plugins folder path.
     *
     * Please change this if you know what you are doing.
     */
    public const PLUGIN_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR;
}
