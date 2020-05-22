<?php

declare(strict_types=1);

namespace MohsenJS\Plugins;

use MohsenJS\EventHandler;

abstract class Plugin
{
    /**
     * MadelineProto object.
     *
     * @var EventHandler
     */
    protected $MadelineProto;

    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'plugin description';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = 'plugin pattern';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = 'plugin usage';

    /**
     * the results of regex pattern.
     *
     * @var string[]
     */
    protected $matches = [];

    /**
     * If this plugin is enabled.
     *
     * @var bool
     */
    protected $enabled = true;

    /**
     * Constructor.
     *
     * @param EventHandler $MadelineProto
     */
    public function __construct(EventHandler $MadelineProto)
    {
        $this->MadelineProto = $MadelineProto;
    }

    /**
     * Execute plugin.
     *
     * @return \Generator
     */
    abstract public function execute(): \Generator;

    /**
     * Get plugin name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get plugin description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get plugin pattern.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Get plugin usage.
     *
     * @return string
     */
    public function getUsage(): string
    {
        return $this->usage;
    }

    /**
     * Set the results of regex pattern.
     *
     * @param string[]|null $matches
     *
     * @return void
     */
    public function setMatches(?array $matches): void
    {
        $this->matches = \is_array($matches) ? $matches : [];
    }

    /**
     * Get the results of regex pattern.
     *
     * @return string[]
     */
    public function getMatches(): array
    {
        return $this->matches;
    }

    /**
     * Check if plugin is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
