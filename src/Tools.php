<?php

declare(strict_types=1);

namespace MohsenJS;

final class Tools
{
    /**
     * Looks for suffixes in strings in a case-insensitive way.
     *
     * @param string $value
     * @param string $suffix
     *
     * @return bool
     */
    public static function hasSuffix(string $value, string $suffix): bool
    {
        return \strcasecmp($suffix, \substr($value, -\strlen($suffix))) === 0;
    }

    /**
     * add suffix to input string and ensures that the given string ends with the given suffix.
     * If the string already contains the suffix, it's not added twice. It's case-insensitive.
     *
     * @param string $value
     * @param string $suffix
     *
     * @return string
     */
    public static function addSuffix(string $value, string $suffix): string
    {
        return self::removeSuffix($value, $suffix) . $suffix;
    }

    /**
     * remove suffix from input string and ensures that the given string doesn't end with the given suffix.
     * If the string contains the suffix multiple times, only the last one is removed.
     * It's case-insensitive.
     *
     * @param string $value
     * @param string $suffix
     *
     * @return string
     */
    public static function removeSuffix(string $value, string $suffix): string
    {
        return self::hasSuffix($value, $suffix) ? \substr($value, 0, -\strlen($suffix)) : $value;
    }

    /**
     * Sanitize plugin name. (e.g `hello_world_plugin` -> `HelloWorldPlugin`).
     *
     * @param string $plugin
     *
     * @return string
     */
    public static function sanitizePlugin(string $plugin): string
    {
        return (string) \str_replace(' ', '', self::ucwordsUnicode((string) \str_replace('_', ' ', $plugin)));
    }

    /**
     * Replace function `ucwords` for UTF-8 characters in the class definition and plugins.
     *
     * @param string $str
     * @param string $encoding (default = 'UTF-8')
     *
     * @return string
     */
    public static function ucwordsUnicode(string $str, string $encoding = 'UTF-8'): string
    {
        return (string) \mb_convert_case($str, MB_CASE_TITLE, $encoding);
    }

    /**
     * Replace function `ucfirst` for UTF-8 characters in the class definition and plugins.
     *
     * @param string $str
     * @param string $encoding (default = 'UTF-8')
     *
     * @return string
     */
    public static function ucfirstUnicode(string $str, string $encoding = 'UTF-8'): string
    {
        return \mb_strtoupper(\mb_substr($str, 0, 1, $encoding), $encoding)
            . \mb_strtolower(\mb_substr($str, 1, \mb_strlen($str), $encoding), $encoding);
    }

    /**
     * Transforms the given string into the format commonly used by this library
     * (e.g. `app:do_this-and_that` -> `Appdothisandthat`) but it doesn't check
     * the validity of the class name.
     *
     * @param string $value
     * @param string $suffix
     *
     * @return string
     */
    public static function generateClassName(string $value, string $suffix = 'Plugin'): string
    {
        $value = (string) \str_replace(['-', '_', '.', ':', ' '], '', $value);
        $value = self::ucfirstUnicode($value);

        return self::addSuffix($value, $suffix);
    }

    /**
     * Escape Markdown or HTML special characters.
     *
     * @param string $str       input string
     * @param string $cleanMode clean mode `Markdown` or `HTML`
     *
     * @return string
     */
    public static function clean(string $str, string $cleanMode): string
    {
        $cleanMode = \strtolower($cleanMode);
        if ($cleanMode === 'html') {
            return \htmlspecialchars($str, ENT_QUOTES);
        }
        if ($cleanMode === 'markdown') {
            return (string) \str_replace(['[', '`', '*', '_'], ['\[', '\`', '\*', '\_'], $str);
        }

        return $str;
    }

    /**
     * Create data folder if it doesn't exists.
     *
     * @return void
     */
    public static function checkDataPath(): void
    {
        if (! \is_dir(Config::DATA_PATH)) {
            \mkdir(Config::DATA_PATH, 0700);
            \file_put_contents(
                Config::DATA_PATH . '.htaccess',
                "order deny,allow\ndeny from all\nallow from 127.0.0.1"
            );
        }
    }
}
