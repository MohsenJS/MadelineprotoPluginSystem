<?php

declare(strict_types=1);

namespace OxMohsen\Plugins\AdminPlugins;

use OxMohsen\Conversation;
use danog\MadelineProto\Logger;
use OxMohsen\Plugins\AdminPlugin;

final class BroadcastPlugin extends AdminPlugin
{
    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'broadcast';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'send a message to all chats.';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]broadcast$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!broadcast';

    /**
     * Conversation Object.
     *
     * @var \OxMohsen\Conversation
     */
    private $conversation;

    public function execute(): \Generator
    {
        $message = 'An error occurred :(';
        $user_id = $this->MadelineProto->update->getFromId();
        $chat_id = $this->MadelineProto->update->getChatId();

        if (0 !== $user_id && 0 !== $chat_id) {
            $this->conversation = new Conversation(
                $this->MadelineProto->dataStoredOnDb,
                $user_id,
                $chat_id,
                $this->getName()
            );

            yield $this->conversation->start();
            $message = yield $this->checkStep();
        }

        yield $this->MadelineProto->messages->sendMessage([
            'peer'            => $this->MadelineProto->update->getUpdate()->toArray(),
            'message'         => $message,
            'reply_to_msg_id' => $this->MadelineProto->update->getMessageId(),
        ]);
    }

    /**
     * Get the message and send it to all chats.
     */
    private function broadcastMessage(): \Generator
    {
        $method = 'Text' === $this->MadelineProto->update->getMessageType() ? 'sendMessage' : 'sendMedia';
        $params = [];

        foreach (yield $this->MadelineProto->getDialogs() as $peer) {
            $tmp = ['peer' => $peer, 'message' => $this->MadelineProto->update->getText()];
            if ('sendMedia' === $method) {
                $tmp['media'] = $this->MadelineProto->update->getUpdate()->get('message.media')->getArray();
            }
            $params[] = $tmp;
        }
        $params['multiple'] = true;

        try {
            yield $this->MadelineProto->messages->{$method}($params);
        } catch (\Throwable $error) {
            $this->MadelineProto->logger($error->getMessage(), Logger::ERROR);
        }

        yield $this->conversation->stop();
    }

    /**
     * Get appropriate message with user step.
     */
    private function checkStep(): \Generator
    {
        $note = yield $this->conversation->getSavedNote();

        if (null === $note) {
            yield $this->conversation->update('getUserMessage');

            return 'Please send the message that you want to send to all of your chats:'.
                PHP_EOL.'Send "!stop" to cancel';
        }
        if ('getUserMessage' === $note && '!stop' !== $this->MadelineProto->update->getText()) {
            yield $this->broadcastMessage();

            return 'Message sent to all chats.';
        }
        if ('getUserMessage' === $note && '!stop' === $this->MadelineProto->update->getText()) {
            yield $this->conversation->stop();

            return 'Successfully canceled.';
        }

        return 'An error occurred :(';
    }
}
