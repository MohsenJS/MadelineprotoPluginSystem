<?php

declare(strict_types=1);

namespace MohsenJS;

use MohsenJS\Plugins\Plugin;
use danog\MadelineProto\Logger;

use function Amp\File\isdir;

final class EventHandler extends \danog\MadelineProto\EventHandler
{
    /**
     * Current Update.
     *
     * @var array
     */
    protected $update = [];

    /**
     * Custom plugins paths.
     *
     * @var string[]
     */
    protected $plugins_paths = [];

    /**
     * Get peer(s) where to report errors.
     *
     * @return int|string|array
     */
    public function getReportPeers()
    {
        return Config::ADMINS;
    }

    /**
     * Handle updates from supergroups and channels.
     *
     * @param array $update Update
     *
     * @return \Generator
     */
    public function onUpdateNewChannelMessage(array $update): \Generator
    {
        $this->update = $update;
        yield $this->run();
    }

    /**
     * Handle updates from users.
     *
     * @param array $update Update
     *
     * @return \Generator
     */
    public function onUpdateNewMessage(array $update): \Generator
    {
        $this->update = $update;
        yield $this->run();
    }

    /**
     * Finds and runs the plugin.
     *
     * @return \Generator
     */
    public function run(): \Generator
    {
        $plugin_obj = yield $this->userHaveActiveConversation();
        if ($plugin_obj === null) {
            if ($this->isAdmin()) {
                yield $this->addPluginsPath(Config::PLUGIN_PATH . 'AdminPlugins');
            }
            yield $this->addPluginsPath(Config::PLUGIN_PATH . 'UserPlugins');
            $plugin_obj = $this->checkMessage();
        }

        if ($plugin_obj !== null && $plugin_obj->isEnabled()) {
            try {
                yield $plugin_obj->execute();
            } catch (\Throwable $error) {
                Logger::log($error->getMessage(), Logger::ERROR);
            }
        }
    }

    /**
     * check current user have an acive conversation or not.
     *
     * @return \Generator `Plugin` object if the current user have an acive conversation, `null` otherwise
     */
    public function userHaveActiveConversation(): \Generator
    {
        if (isset($this->update['message']['from_id'])) {
            $Conversation = new Conversation((int) $this->update['message']['from_id']);
            yield $Conversation->start();
            if ($Conversation->haveConversation()) {
                return $this->getPluginObject((string) $Conversation->getSavedPlugin());
            }
        }

        return null;
    }

    /**
     * Add a custom plugins path.
     *
     * @param string $path Custom plugins path to add
     *
     * @return \Generator
     */
    public function addPluginsPath(string $path): \Generator
    {
        if (yield isdir($path) && ! \in_array($path, $this->plugins_paths, true)) {
            $this->plugins_paths[] = $path;
        }
    }

    /**
     * Get the list of plugins.
     *
     * @return \Generator<Plugin>
     */
    public function getPluginsList(): \Generator
    {
        foreach ($this->plugins_paths as $path) {
            $files = new \RegexIterator(
                new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)),
                '/^.+Plugin.php$/'
            );
            /**
             * @var \DirectoryIterator[] $files
             */
            foreach ($files as $file) {
                $plugin = Tools::sanitizePlugin(\substr($file->getFilename(), 0, -10));
                include_once $file->getPathname();
                $plugin_obj = $this->getPluginObject($plugin);
                if ($plugin_obj instanceof Plugin) {
                    yield $plugin_obj;
                }
            }
        }
    }

    /**
     * Get an object instance of the passed plugin name.
     *
     * @param string $plugin
     *
     * @return Plugin|null
     */
    public function getPluginObject(string $plugin): ?Plugin
    {
        $which = [];
        if ($this->isAdmin()) {
            $which[] = 'Admin';
        }
        $which[] = 'User';

        /** @var string $auth plugin role */
        foreach ($which as $auth) {
            $plugin_namespace = __NAMESPACE__ . '\\Plugins\\' . $auth . 'Plugins\\' .
                Tools::ucfirstUnicode($plugin) . 'Plugin';
            if (\class_exists($plugin_namespace)) {
                return new $plugin_namespace($this, $this->update);
            }
        }

        return null;
    }

    /**
     * Check incoming message text with plugin regex patten.
     *
     * @return Plugin|null `Plugin` object if the pattern matches incoming text, `null` otherwise
     */
    public function checkMessage(): ?Plugin
    {
        foreach ($this->getPluginsList() as $plugin) {
            if (@\preg_match($plugin->getPattern(), $plugin->getText(), $matches) && $plugin->isEnabled()) {
                if ($matches !== null) {
                    $plugin->setMatches($matches);
                }

                return $plugin;
            }
        }

        return null;
    }

    /**
     * Check if the passed user is an admin.
     *
     * If no user id is passed, the current update is checked for a valid message sender.
     *
     * @param int|null $user_id
     *
     * @return bool
     */
    public function isAdmin(?int $user_id = null): bool
    {
        if ($user_id === null) {
            $user_id = $this->update['message']['from_id'] ?? null;
        }

        return \in_array($user_id, Config::ADMINS);
    }
}
