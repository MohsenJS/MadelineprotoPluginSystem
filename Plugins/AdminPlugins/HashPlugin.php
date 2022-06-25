<?php

declare(strict_types=1);

namespace OxMohsen\Plugins\AdminPlugins;

use OxMohsen\Plugins\AdminPlugin;

final class HashPlugin extends AdminPlugin
{
    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'hash';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'find the md5, sha1, sha256, sha512 of the string.';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]hash (.+)$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!hash text';

    public function execute(): \Generator
    {
        $text    = $this->getMatches()[1];
        $message = $this->getHash($text);

        yield $this->MadelineProto->messages->sendMessage([
            'peer'            => $this->MadelineProto->update->getUpdate()->toArray(),
            'message'         => $message,
            'parse_mode'      => 'Markdown',
            'reply_to_msg_id' => $this->MadelineProto->update->getMessageId(),
        ]);
    }

    /**
     * Get hash of text.
     */
    private function getHash(string $text): string
    {
        $message = '';
        foreach (['md5', 'sha1', 'sha256', 'sha512'] as $algo) {
            $message .= strtoupper($algo).': `'.hash($algo, $text).'`'.PHP_EOL.PHP_EOL;
        }

        return trim($message);
    }
}
