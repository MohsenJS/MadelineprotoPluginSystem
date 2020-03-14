<?php

declare(strict_types=1);

namespace MohsenJS\Plugins\AdminPlugins;

use MohsenJS\Plugins\AdminPlugin;

final class DeletemessagesPlugin extends AdminPlugin
{
    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'deletemessages';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'delete some messages of supergroup or channel';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]delmsgs ([\d]+)$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!delmsgs 100';

    public function execute(): \Generator
    {
        $message      = 'i can\'t delete message here.';
        $canDelete    = yield $this->canDeleteMessage();
        $isSupergroup = yield $this->isChannelOrSupergroup();

        if ($canDelete && $isSupergroup) {
            yield $this->MadelineProto->messages->sendMessage([
                'peer'    => $this->getUpdate(),
                'message' => 'Deleting messages ...',
            ]);
            $countOfDeletedMessages = (int) yield $this->deleteMessages();

            $message = "{$countOfDeletedMessages} messages successfully deleted.";
        }

        yield $this->MadelineProto->messages->sendMessage([
            'peer'            => $this->getUpdate(),
            'message'         => $message,
            'reply_to_msg_id' => $this->getMessageId(),
        ]);
    }

    /**
     * Checks if bot can delete message.
     *
     * @return \Generator `true` if bot can delete message, `false` otherwise.
     */
    private function canDeleteMessage(): \Generator
    {
        $channelParticipant = yield $this->MadelineProto->channels->getParticipant([
            'channel' => $this->getUpdate(),
            'user_id' => 'me',
        ]);

        $type      = $channelParticipant['participant']['_']                               ?? null;
        $canDelete = $channelParticipant['participant']['admin_rights']['delete_messages'] ?? false;

        return ($type === 'channelParticipantAdmin' && $canDelete === true) || $type === 'channelParticipantCreator';
    }

    /**
     * Delete messages.
     *
     * @return \Generator count of deleted message.
     */
    private function deleteMessages(): \Generator
    {
        $inputNumber     = (int) $this->getMatches()[1];
        $lastMessageForDelete = $this->getMessageId();

        if ($lastMessageForDelete === 0) {
            return 0;
        }

        $firstMessageForDelete = $lastMessageForDelete - $inputNumber > 0 ? $lastMessageForDelete - $inputNumber : 1;
        $allMessageForDelete = \range($firstMessageForDelete, $lastMessageForDelete);

        foreach (\array_chunk($allMessageForDelete, 40) as $ids) {
            yield $this->MadelineProto->channels->deleteMessages([
                'channel' => $this->getUpdate(),
                'id'      => $ids,
            ]);
            yield $this->MadelineProto->sleep(\mt_rand(1, 3));
        }

        return $inputNumber;
    }

    /**
     * Checks if current chat is `supergroup` or `channel`.
     *
     * @return \Generator `true` if the current chat is `supergroup` or `channel`, `false` otherwise.
     */
    private function isChannelOrSupergroup(): \Generator
    {
        $type = yield $this->MadelineProto->getInfo($this->getUpdate())['type'];

        return $type === 'supergroup' || $type === 'channel';
    }
}
