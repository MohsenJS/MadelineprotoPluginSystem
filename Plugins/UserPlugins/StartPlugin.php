<?php

declare(strict_types=1);

namespace OxMohsen\Plugins\UserPlugins;

use OxMohsen\Plugins\UserPlugin;

final class StartPlugin extends UserPlugin
{
    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'start';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'start the bot';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]start$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!start';

    public function execute(): \Generator
    {
        yield $this->MadelineProto->messages->sendMessage([
            'peer'            => $this->MadelineProto->update->getChatId(),
            'message'         => 'Hi, please send <code>!help</code> to see my ability.',
            'reply_to_msg_id' => $this->MadelineProto->update->getMessageId(),
            'parse_mode'      => 'HTML',
        ]);
    }
}
