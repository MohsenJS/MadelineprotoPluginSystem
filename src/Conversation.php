<?php

declare(strict_types=1);

namespace MohsenJS;

use Amp\File;

final class Conversation
{
    /**
     * Conversation data.
     *
     * @var array
     */
    private $data = [];

    /**
     * Plugin name.
     *
     * @var string
     */
    private $plugin = '';

    /**
     * user id.
     *
     * @var int
     */
    private $user_id = 0;

    /**
     * Conversation constructor to initialize a new conversation.
     *
     * @param int    $user_id
     * @param string $plugin
     */
    public function __construct(int $user_id, string $plugin = '')
    {
        $this->user_id = $user_id;
        $this->plugin  = $plugin;
    }

    /**
     * Start the Conversation.
     *
     * @return \Generator
     */
    public function start(): \Generator
    {
        if (yield File\exists(Config::DATA_PATH . 'Conversation.data')) {
            $this->data = \unserialize(yield File\get(Config::DATA_PATH . 'Conversation.data'));
        }
    }

    /**
     * Store the user note in the conversation data.
     *
     * @param mixed $note
     *
     * @return \Generator
     */
    public function update($note): \Generator
    {
        $this->data[$this->user_id] = ['plugin' => $this->plugin, 'note' => $note];
        yield $this->save();
    }

    /**
     * Delete the current user conversation.
     *
     * @return \Generator
     */
    public function stop(): \Generator
    {
        unset($this->data[$this->user_id]);
        yield $this->save();
    }

    /**
     * Check if the conversation exists.
     *
     * @return bool
     */
    public function haveConversation(): bool
    {
        return isset($this->data[$this->user_id]);
    }

    /**
     * Retrieve the saved user note.
     *
     * @return mixed|null
     */
    public function getSavedNote()
    {
        return $this->data[$this->user_id]['note'] ?? null;
    }

    /**
     * Retrieve the saved plugin name.
     *
     * @return string
     */
    public function getSavedPlugin(): string
    {
        return $this->data[$this->user_id]['plugin'] ?? '';
    }

    /**
     * Save the conversation data to local file.
     *
     * @return \Generator
     */
    private function save(): \Generator
    {
        yield File\put(Config::DATA_PATH . 'Conversation.data', \serialize($this->data));
    }
}
