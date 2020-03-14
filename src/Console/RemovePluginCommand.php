<?php

declare(strict_types=1);

namespace MohsenJS\Console;

use MohsenJS\Tools;
use MohsenJS\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Exception\RuntimeException;

final class RemovePluginCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('plugin:remove')
            ->setDefinition([
                new InputArgument('name', InputArgument::REQUIRED, 'The name of the class'),
                new InputOption('role', null, InputOption::VALUE_REQUIRED, 'The role of plugin ["Admin" or "User"]'),
            ])
            ->setDescription('Remove a plugin class')
            ->setHelp('This command allows you to remove a plugin class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = Validator::validateClassName((string) $input->getArgument('name'));
        $role = Validator::validateRole((string) $input->getOption('role'));

        if (! $this->removePluginFile($name, $role)) {
            throw new RuntimeException('an error occoured');
        }

        $output->writeln('<info>Plugin deleted successfully.</info>');

        return 0;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        /**
         * @var \Symfony\Component\Console\Helper\QuestionHelper $helper
         */
        $helper = $this->getHelper('question');

        if (! $input->getArgument('name')) {
            $name = (string) $helper->ask($input, $output, $this->getPluginClassName());
            $input->setArgument('name', $name);
        }

        if (! $input->getOption('role')) {
            $role = (string) $helper->ask($input, $output, $this->getPluginRole());
            $input->setOption('role', $role);
        }
    }

    /**
     * Get plugin name from user.
     *
     * @return Question
     */
    protected function getPluginClassName(): Question
    {
        $question = new Question('Please enter plugin class name: ');
        $question->setValidator(
            static function ($answer) {
                return Validator::validateClassName($answer);
            }
        );

        return $question;
    }

    /**
     * Get plugin role from user.
     *
     * @return Question
     */
    protected function getPluginRole(): Question
    {
        $question =  new ChoiceQuestion('Please select plugin role (defaults to Admin)', ['Admin', 'User'], 0);
        $question->setErrorMessage('"%s" is not a valid role.');

        return $question;
    }

    /**
     * Remove plugin file with related info.
     *
     * @param string $name
     * @param string $role
     *
     * @return bool
     */
    protected function removePluginFile(string $name, string $role): bool
    {
        $className = Tools::generateClassName($name);
        $fileName  = Config::PLUGIN_PATH . $role . 'Plugins/' . $className . '.php';

        if (! Validator::pluginFileExists($fileName, $role)) {
            throw new RuntimeException(
                \sprintf('File "%s" dosen\'t exists.', $fileName)
            );
        }

        return \unlink($fileName);
    }
}
