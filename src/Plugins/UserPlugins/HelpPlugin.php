<?php

declare(strict_types=1);

namespace MohsenJS\Plugins\UserPlugins;

use MohsenJS\Tools;
use MohsenJS\Plugins\UserPlugin;

final class HelpPlugin extends UserPlugin
{
    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'help';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'show bot help';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]help$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!help';

    public function execute(): \Generator
    {
        $message = $this->getHelpMessage();
        if ($message === '') {
            $message = 'Nothing to send :(';
        }

        yield $this->MadelineProto->messages->sendMessage([
            'peer'            => $this->MadelineProto->update->getUpdate(),
            'message'         => $message,
            'reply_to_msg_id' => $this->MadelineProto->update->getMessageId(),
            'parse_mode'      => 'HTML',
        ]);
    }

    /**
     * Get all plugins help.
     *
     * @return string on failure `empty string` will be returned
     */
    private function getHelpMessage(): string
    {
        $message = '';
        foreach ($this->MadelineProto->plugins as $role => $plugins) {
            if ($this->MadelineProto->isAdmin() === false && $role === 'admin_plugin') {
                continue;
            }

            /**
             * @var \MohsenJS\Plugins\Plugin $plugin
             */
            foreach ($plugins as $plugin) {
                $message .= \sprintf(
                    '<b>%s</b><br>├ <b>Description:</b> %s<br>├ <b>Usage:</b> <pre>%s</pre><br>└ <b>Status:</b> %s',
                    Tools::clean($plugin->getName(), 'html'),
                    Tools::clean($plugin->getDescription(), 'html'),
                    Tools::clean($plugin->getUsage(), 'html'),
                    $plugin->isEnabled() ? '✅ Active' : '❌ Inactive'
                ) . '<br><br>';
            }
        }

        return $message;
    }
}
