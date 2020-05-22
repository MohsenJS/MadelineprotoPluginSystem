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

final class MakePluginCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('plugin:make')
            ->setDefinition([
                new InputArgument('name', InputArgument::REQUIRED, 'The name of the class'),
                new InputOption('description', null, InputOption::VALUE_REQUIRED, 'Plugin description'),
                new InputOption('pattern', null, InputOption::VALUE_REQUIRED, 'Plugin regex pattern'),
                new InputOption('usage', null, InputOption::VALUE_REQUIRED, 'Plugin usage'),
                new InputOption('role', null, InputOption::VALUE_REQUIRED, 'The role of plugin ["Admin" or "User"]'),
            ])
            ->setDescription('Create a new plugin class')
            ->setHelp('This command allows you to create a new plugin class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name        = Validator::validateClassName((string) $input->getArgument('name'));
        $description = (string) $input->getOption('description');
        $pattern     = Validator::validateRegexPattern((string) $input->getOption('pattern'));
        $usage       = (string) $input->getOption('usage');
        $role        = Validator::validateRole((string) $input->getOption('role'));

        if ($this->generatePluginFile($name, $description, $pattern, $usage, $role) === false) {
            throw new \RuntimeException('an error occurred');
        }

        $output->writeln('<info>Plugin added successfully.</info>');

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
        if (! $input->getOption('description')) {
            $description = (string) $helper->ask($input, $output, $this->getPluginDescription());
            $input->setOption('description', $description);
        }
        if (! $input->getOption('pattern')) {
            $pattern = (string) $helper->ask($input, $output, $this->getPluginPattern());
            $input->setOption('pattern', $pattern);
        }
        if (! $input->getOption('usage')) {
            $usage = (string) $helper->ask($input, $output, $this->getPluginUsage());
            $input->setOption('usage', $usage);
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
     * Get plugin description from user.
     *
     * @return Question
     */
    protected function getPluginDescription(): Question
    {
        return new Question('Please enter plugin description: ');
    }

    /**
     * Get plugin regex pattern from user.
     *
     * @return Question
     */
    protected function getPluginPattern(): Question
    {
        $question = new Question('Please plugin regex pattern: ');
        $question->setValidator(
            static function ($answer) {
                return Validator::validateRegexPattern("/^[\!\#\.\/]{$answer}$/i");
            }
        );

        return $question;
    }

    /**
     * Get plugin usage from user.
     *
     * @return Question
     */
    protected function getPluginUsage(): Question
    {
        return new Question('Please enter plugin usage (This will help the user to find out how to use this plugin): ');
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
     * Generate plugin file with related info.
     *
     * @param string $name
     * @param string $description
     * @param string $pattern
     * @param string $usage
     * @param string $role
     *
     * @return bool
     */
    protected function generatePluginFile(
        string $name,
        string $description,
        string $pattern,
        string $usage,
        string $role
    ): bool {
        $className = Tools::generateClassName($name);
        $fileName  = Config::PLUGIN_PATH . $role . 'Plugins/' . $className . '.php';

        if (Validator::pluginFileExists($fileName, $role)) {
            throw new RuntimeException(\sprintf('File "%s" already exists.', $fileName));
        }

        $template = \file_get_contents(__DIR__ . '/template');
        if ($template === false) {
            throw new RuntimeException(\sprintf('Could not find "template" file in "%s"', __DIR__));
        }

        $writhed = \file_put_contents(
            $fileName,
            \sprintf($template, $role, $role, $className, $role, \strtolower($name), $description, $pattern, $usage)
        );

        return $writhed === false ? false : true;
    }
}
