<?php

declare(strict_types=1);

namespace OxMohsen\Console;

use OxMohsen\Tools;
use Symfony\Component\Console\Exception\InvalidArgumentException;

final class Validator
{
    /**
     * Keywords that used by php.
     *
     * @var string[]
     */
    private static $phpKeywords = [
        '__halt_compiler', 'abstract', 'and', 'array',
        'as', 'break', 'callable', 'case', 'catch', 'class',
        'clone', 'const', 'continue', 'declare', 'default', 'die', 'do',
        'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor',
        'endforeach', 'endif', 'endswitch', 'endwhile', 'eval',
        'exit', 'extends', 'final', 'finally', 'for', 'foreach', 'function',
        'global', 'goto', 'if', 'implements', 'include',
        'include_once', 'instanceof', 'insteadof', 'interface', 'isset',
        'list', 'namespace', 'new', 'or', 'print', 'private',
        'protected', 'public', 'require', 'require_once', 'return',
        'static', 'switch', 'throw', 'trait', 'try', 'unset',
        'use', 'var', 'while', 'xor', 'yield',
        'int', 'float', 'bool', 'string', 'true', 'false', 'null', 'void',
        'iterable', 'object', '__file__', '__line__', '__dir__', '__function__', '__class__',
        '__method__', '__namespace__', '__trait__', 'self', 'parent',
    ];

    /**
     * Check that the input string is a valid class name.
     */
    public static function validateClassName(string $className): string
    {
        if (! mb_check_encoding($className, 'UTF-8')) {
            throw new InvalidArgumentException(sprintf('"%s" is not a UTF-8-encoded string.', $className));
        }

        if (! preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $className)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is not valid as a PHP class name.'.PHP_EOL.
                    'it must start with a letter or underscore,followed by any number of letters,numbers,underscores',
                $className
            ));
        }

        if (\in_array(strtolower($className), self::$phpKeywords, true)) {
            throw new InvalidArgumentException(
                sprintf('"%s" is a reserved keyword and thus cannot be used as class name in PHP.', $className)
            );
        }

        return $className;
    }

    /**
     * Check that the input string is a valid regex pattern.
     */
    public static function validateRegexPattern(string $pattern): string
    {
        if (false === @preg_match($pattern, 'OxMohsen')) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid regex pattern.', $pattern));
        }

        return $pattern;
    }

    /**
     * Check that the input string is a valid plugin role.
     */
    public static function validateRole(string $role): string
    {
        $role = Tools::ucfirstUnicode($role);
        if (! \in_array($role, ['Admin', 'User'])) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid role.', $role));
        }

        return $role;
    }

    /**
     * Check that the plugin file exists.
     */
    public static function pluginFileExists(string $pluginFileName, string $auth = 'User'): bool
    {
        if ('User' !== $auth && 'Admin' !== $auth) {
            throw new InvalidArgumentException('Plugin auth must be "Admin" or "User"');
        }

        return is_file($pluginFileName) ? true : false;
    }
}
