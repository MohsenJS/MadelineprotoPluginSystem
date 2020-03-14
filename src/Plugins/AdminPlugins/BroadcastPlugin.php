<?php

declare(strict_types=1);

namespace MohsenJS\Plugins\AdminPlugins;

use MohsenJS\Conversation;
use danog\MadelineProto\Logger;
use MohsenJS\Plugins\AdminPlugin;

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
    protected $description = 'send a message to all chats';

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
     * @var \MohsenJS\Conversation
     */
    private $conversation;

    public function execute(): \Generator
    {
        $message = 'An error occurred :(';

        $user_id = $this->getFromId();
        if ($user_id !== 0) {
            $this->conversation = new Conversation($user_id, $this->getName());
            yield $this->conversation->start();
            $message = yield $this->checkStep();
        }

        yield $this->MadelineProto->messages->sendMessage([
            'peer'            => $this->getUpdate(),
            'message'         => $message,
            'reply_to_msg_id' => $this->getMessageId(),
        ]);
    }

    /**
     * Get the message and send it to all chats.
     *
     * @return \Generator
     */
    private function brodcastMessage(): \Generator
    {
        $method  = $this->getMessageType() === 'Text' ? 'sendMessage' : 'sendMedia';
        $params  = [];

        foreach (yield $this->MadelineProto->getDialogs() as $peer) {
            $tmp = ['peer' => $peer, 'message' => $this->getText()];
            if ($method === 'sendMedia') {
                $tmp['media'] = $this->getUpdate();
            }
            $params[] = $tmp;
        }
        $params['multiple'] = true;

        try {
            yield $this->MadelineProto->messages->{$method}($params);
        } catch (\Throwable $error) {
            Logger::log($error->getMessage(), Logger::ERROR);
        }

        yield $this->conversation->stop();
    }

    /**
     * Get appropriate message with user step.
     *
     * @return \Generator
     */
    private function checkStep(): \Generator
    {
        $note = $this->conversation->getSavedNote();
        if ($note === null) {
            yield $this->conversation->update('getUserMessage');

            return 'Please send the message that you want to send to all of your chats:' .
            PHP_EOL . 'Send "!stop" to cancel';
        }
        if ($note === 'getUserMessage' && $this->getText() !== '!stop') {
            yield $this->brodcastMessage();

            return 'Message sent to all chats.';
        }
        if ($note === 'getUserMessage' && $this->getText() === '!stop') {
            yield $this->conversation->stop();

            return 'Successfully canceled.';
        }

        return 'An error occurred :(';
    }
}
