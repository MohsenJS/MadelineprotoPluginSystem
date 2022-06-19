<?php

declare(strict_types=1);

namespace OxMohsen\Console;

use OxMohsen\Config;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ListPluginCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('plugin:list')
            ->setDescription('List all plugins');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $allPlugin = $this->getAllPlugins();
        $table     = new Table($output);
        $table
            ->setHeaders(['Admin Plugins', 'User Plugins'])
            ->setRows($allPlugin);
        $table->render();

        return 0;
    }

    /**
     * List all plugins.
     *
     * @return array<int, array<int, string>>
     */
    protected function getAllPlugins(): array
    {
        $allPlugins = [];
        foreach ([Config::PLUGIN_PATH.'AdminPlugins', Config::PLUGIN_PATH.'UserPlugins'] as $path) {
            $index = 0;
            $files = new \RegexIterator(
                new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)),
                '/^.+Plugin.php$/'
            );

            foreach ($files as $file) {
                $allPlugins[$index][] = strtolower(substr($file->getFilename(), 0, -10));
                ++$index;
            }
        }

        return $allPlugins;
    }
}
