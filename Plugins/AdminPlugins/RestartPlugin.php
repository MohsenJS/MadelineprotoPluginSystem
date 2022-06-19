<?php

declare(strict_types=1);

namespace OxMohsen\Plugins\AdminPlugins;

use OxMohsen\Plugins\AdminPlugin;

final class RestartPlugin extends AdminPlugin
{
    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'restart';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'forcefully restart and apply changes. (Only work if running via web)';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]restart$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!restart';

    /**
     * If this plugin is enabled.
     *
     * @var bool
     */
    protected $enabled = true;

    public function execute(): \Generator
    {
        yield $this->MadelineProto->restart();

        yield $this->MadelineProto->messages->sendMessage([
            'peer'            => $this->MadelineProto->update->getChatId(),
            'message'         => 'The bot successfully restarted.',
            'reply_to_msg_id' => $this->MadelineProto->update->getMessageId(),
        ]);
    }
}
