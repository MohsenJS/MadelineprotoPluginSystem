<?php

declare(strict_types=1);

namespace OxMohsen;

use OxMohsen\Plugins\Plugin;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Db\DbArray;
use danog\MadelineProto\EventHandler;

final class PluginEventHandler extends EventHandler
{
    /**
     * Current Update.
     *
     * @var Update
     */
    public $update;

    /**
     * All Plugins.
     *
     * @var array
     */
    public $plugins = [
        'admin_plugin' => [],
        'user_plugin'  => [],
    ];

    /**
     * @var DbArray<array>
     */
    public $dataStoredOnDb;

    /**
     * List of properties automatically stored in database (MySQL, Postgres, redis or memory).
     *
     * @see https://docs.madelineproto.xyz/docs/DATABASE.html
     */
    protected static array $dbProperties = [
        'dataStoredOnDb' => 'array',
    ];

    /**
     * Get peer(s) where to report errors.
     */
    public function getReportPeers(): array
    {
        return Config::ADMINS;
    }

    /**
     * Load all plugins.
     */
    public function onStart(): \Generator
    {
        $this->logger('The bot was started!');
        $this->plugins['admin_plugin'] = yield $this->getPlugins(Config::PLUGIN_PATH.'AdminPlugins');
        $this->plugins['user_plugin']  = yield $this->getPlugins(Config::PLUGIN_PATH.'UserPlugins');
    }

    /**
     * Handle updates from supergroups and channels.
     *
     * @param array $update Update
     */
    public function onUpdateNewChannelMessage(array $update): \Generator
    {
        return $this->onUpdateNewMessage($update);
    }

    /**
     * Handle updates from users.
     *
     * @param array $update Update
     */
    public function onUpdateNewMessage(array $update): \Generator
    {
        $this->update = new Update($update);

        if (
            'messageEmpty' === $this->update->getUpdate()->get('message._')
            || $this->update->getUpdate()->get('message.out') ?? false
        ) {
            return;
        }

        yield $this->run();
    }

    /**
     * Finds and runs the plugin.
     */
    public function run(): \Generator
    {
        $plugin_obj = yield $this->userHaveActiveConversation();
        if (null === $plugin_obj) {
            $plugin_obj = $this->checkMessage();
        }

        if (null !== $plugin_obj) {
            try {
                yield $plugin_obj->execute();
            } catch (\Throwable $error) {
                $this->logger($error->getMessage(), Logger::ERROR);
            }
        }
    }

    /**
     * check current user have an active conversation or not.
     *
     * @return \Generator `Plugin` object if the current user have an active conversation, `null` otherwise
     */
    public function userHaveActiveConversation(): \Generator
    {
        $Conversation = new Conversation($this->dataStoredOnDb, $this->update->getFromId(), $this->update->getChatId());
        if (yield $Conversation->haveConversation()) {
            return $this->getPluginObject(yield $Conversation->getSavedPlugin());
        }

        return null;
    }

    /**
     * Get plugin object of entered path.
     *
     * @return Plugin[]
     */
    public function getPlugins(string $path)
    {
        $plugins = [];
        $files   = new \RegexIterator(
            new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)),
            '/^.+Plugin.php$/'
        );

        /**
         * @var \DirectoryIterator[] $files
         */
        foreach ($files as $file) {
            $plugin     = Tools::sanitizePlugin(substr($file->getFilename(), 0, -10));
            $plugin_obj = $this->getPluginObject($plugin);
            if ($plugin_obj instanceof Plugin) {
                $plugins[] = $plugin_obj;
            }
        }

        return $plugins;
    }

    /**
     * Get an object instance of the passed plugin name.
     */
    public function getPluginObject(string $plugin): ?Plugin
    {
        /** @var string $auth plugin role */
        foreach (['Admin', 'User'] as $auth) {
            $plugin_namespace = __NAMESPACE__.'\\Plugins\\'.$auth.'Plugins\\'.Tools::ucfirstUnicode($plugin).'Plugin';
            if (class_exists($plugin_namespace)) {
                return new $plugin_namespace($this);
            }
        }

        return null;
    }

    /**
     * Check incoming message text with plugin regex patten.
     *
     * @return null|Plugin `Plugin` object if the pattern matches incoming text, `null` otherwise
     */
    public function checkMessage(): ?Plugin
    {
        $isAdmin = $this->isAdmin();
        foreach ($this->plugins as $role => $plugins) {
            if ('admin_plugin' === $role && false === $isAdmin) {
                continue;
            }

            /**
             * @var Plugin $plugin
             */
            foreach ($plugins as $plugin) {
                if (@preg_match($plugin->getPattern(), $this->update->getText(), $matches) && $plugin->isEnabled()) {
                    $plugin->setMatches($matches);

                    return $plugin;
                }
            }
        }

        return null;
    }

    /**
     * Check if the passed user is an admin.
     *
     * If no user id is passed, the current update is checked for a valid message sender.
     */
    public function isAdmin(?int $user_id = null): bool
    {
        $user_id = $user_id ?? $this->update->getFromId();

        return \in_array($user_id, Config::ADMINS);
    }
}
