<?php

declare(strict_types=1);

namespace OxMohsen;

use danog\MadelineProto\Db\DbArray;

final class Conversation
{
    /**
     * Conversation data.
     *
     * @var DbArray<string>
     */
    private $data;

    /**
     * Plugin name.
     *
     * @var string
     */
    private $plugin;

    /**
     * user id.
     *
     * @var int
     */
    private $user_id;

    /**
     * chat id.
     *
     * @var int
     */
    private $chat_id;

    /**
     * Conversation constructor to initialize a new conversation.
     *
     * @param DbArray $data    db $data variables in `\OxMohsen\PluginEventHandler`
     * @param int     $user_id id of user that want to start the conversation
     * @param int     $chat_id id of chat that belong this conversation
     * @param string  $plugin  plugin name that belong this conversation
     */
    public function __construct(DbArray &$data, int $user_id, int $chat_id, string $plugin = '')
    {
        $this->user_id = $user_id;
        $this->chat_id = $chat_id;
        $this->plugin  = $plugin;
        $this->data    = &$data;
    }

    /**
     * Start the Conversation.
     */
    public function start(): \Generator
    {
        $haveConversation = yield $this->haveConversation();
        if (! $haveConversation) {
            $this->update(null);
        }
    }

    /**
     * Store the user note in the conversation data.
     */
    public function update(mixed $note): void
    {
        $note = json_encode($note, JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);

        $this->data["{$this->chat_id}|{$this->user_id}"] = "plugin={$this->plugin}&note={$note}";
    }

    /**
     * Delete the current user conversation.
     */
    public function stop(): void
    {
        unset($this->data["{$this->chat_id}|{$this->user_id}"]);
    }

    /**
     * Check if the conversation exists.
     */
    public function haveConversation(): \Generator
    {
        return yield $this->data->isset("{$this->chat_id}|{$this->user_id}");
    }

    public function getSavedData(): \Generator
    {
        $dbData = yield $this->data["{$this->chat_id}|{$this->user_id}"];
        parse_str($dbData, $savedData);

        return $savedData;
    }

    /**
     * Retrieve the saved user note.
     */
    public function getSavedNote(): \Generator
    {
        return json_decode((yield $this->getSavedData())['note'], true);
    }

    /**
     * Retrieve the saved plugin name.
     */
    public function getSavedPlugin(): \Generator
    {
        return (yield $this->getSavedData())['plugin'];
    }
}
