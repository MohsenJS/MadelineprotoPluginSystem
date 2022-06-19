<?php

declare(strict_types=1);

namespace OxMohsen\Plugins;

use OxMohsen\PluginEventHandler;

abstract class Plugin
{
    /**
     * MadelineProto object.
     *
     * @var PluginEventHandler
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
     */
    public function __construct(PluginEventHandler &$MadelineProto)
    {
        $this->MadelineProto = &$MadelineProto;
    }

    /**
     * Execute plugin.
     */
    abstract public function execute(): \Generator;

    /**
     * Get plugin name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get plugin description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get plugin pattern.
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Get plugin usage.
     */
    public function getUsage(): string
    {
        return $this->usage;
    }

    /**
     * Set the results of regex pattern.
     *
     * @param null|string[] $matches
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
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
