<?php

declare(strict_types=1);

namespace MohsenJS\Plugins\AdminPlugins;

use MohsenJS\Plugins\AdminPlugin;

final class LogoutPlugin extends AdminPlugin
{
    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'logout';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'terminate the robot session';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]logout$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!logout';

    public function execute(): \Generator
    {
        yield $this->MadelineProto->report('the robot is logging out');
        yield $this->MadelineProto->logout();
    }
}
