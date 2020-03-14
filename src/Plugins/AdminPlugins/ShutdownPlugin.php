<?php

declare(strict_types=1);

namespace MohsenJS\Plugins\AdminPlugins;

use MohsenJS\Tools;
use MohsenJS\Config;
use danog\MadelineProto\Magic;
use danog\MadelineProto\Logger;
use MohsenJS\Plugins\AdminPlugin;

final class ShutdownPlugin extends AdminPlugin
{
    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'shutdown';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'shut the bot down';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]shutdown$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!shutdown';

    public function execute(): \Generator
    {
        $name    = yield $this->getUserFullName('HTML');
        $user_id = $this->getFromId();
        $params  = [];
        foreach (Config::ADMINS as $admin) {
            $params[] = [
                'peer'       => $admin,
                'message'    => "<a href=\"mention:{$user_id}\">{$name}</a> shot me down 🥺. Bang Bang",
                'parse_mode' => 'HTML',
            ];
        }
        $params['multiple'] = true;

        try {
            yield $this->MadelineProto->messages->sendMessage($params);
        } catch (\Throwable $error) {
            Logger::log($error->getMessage(), Logger::ERROR);
        }

        Magic::shutdown();
    }

    /**
     * get user full name. you can pass clean mode to escape Markdown or HTML special characters.
     *
     * @param string|null $cleanMode clean mode (`Markdown` or `HTML`)
     *
     * @return \Generator
     */
    private function getUserFullName(?string $cleanMode = null): \Generator
    {
        $info     = yield $this->MadelineProto->getInfo($this->update);
        $fullName = (string) ($info['User']['first_name'] ?? 'No Name');
        isset($info['User']['last_name']) && $fullName .= ' ' . $info['User']['last_name'];
        if ($cleanMode !== null) {
            $fullName = Tools::clean($fullName, $cleanMode);
        }

        return $fullName;
    }
}
