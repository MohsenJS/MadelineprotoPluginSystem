<?php

declare(strict_types=1);

namespace OxMohsen;

use Arrayy\Arrayy as ArrayHelper;

final class Update
{
    /**
     * incoming update.
     *
     * @var ArrayHelper
     */
    public $update;

    /**
     * Update constructor.
     */
    public function __construct(array $update)
    {
        $this->update = new ArrayHelper($update);
    }

    /**
     * Get current update.
     */
    public function getUpdate(): ArrayHelper
    {
        return $this->update;
    }

    /**
     * Get message text.
     *
     * @return string message text if find it, `empty string` otherwise
     */
    public function getText(): string
    {
        return (string) ($this->update->get('message.message') ?? '');
    }

    /**
     * get ID of the sender of the message.
     *
     * @return int from id if find it, `zero` otherwise
     */
    public function getFromId(): int
    {
        return match ($this->update->get('message.from_id._')) {
            'peerChannel' => (int) ('-100'.$this->update->get('message.from_id.channel_id') ?? 0),
            'peerChat'    => (int) (-1 * $this->update->get('message.from_id.chat_id')      ?? 0),
            'peerUser'    => (int) ($this->update->get('message.from_id.user_id')           ?? 0),
            default       => 0,
        };
    }

    /**
     * get the id of the chat where this message was sent.
     *
     * @return int chat id if find it, `zero` otherwise
     */
    public function getChatId(): int
    {
        return match ($this->update->get('message.peer_id._')) {
            'peerChannel' => (int) ('-100'.$this->update->get('message.peer_id.channel_id') ?? 0),
            'peerChat'    => (int) (-1 * $this->update->get('message.peer_id.chat_id')      ?? 0),
            'peerUser'    => (int) ($this->update->get('message.peer_id.user_id')           ?? 0),
            default       => 0,
        };
    }

    /**
     * get ID of the message.
     *
     * @return int message id if find it, `zero` otherwise
     */
    public function getMessageId(): int
    {
        return (int) ($this->update->get('message.id') ?? 0);
    }

    /**
     * get ID of message to which this message is replying.
     *
     * @return int reply message id if find it, `zero` otherwise
     */
    public function getReplyMessageId(): int
    {
        return (int) ($this->update->get('message.reply_to.reply_to_msg_id') ?? 0);
    }

    /**
     * Get media type.
     *
     * Media type could be `Empty`, `Photo`, `Geo`, `Contact`, `Unsupported`, `Document`,
     * `WebPage`, `Venue`, `Game`, `Invoice`, `GeoLive`, `Poll` and `Dice`.
     *
     * @return string media type if find it, `empty string` otherwise
     */
    public function getMediaType(): ?string
    {
        return substr($this->update->get('message.media._') ?? '', 12);
    }

    /**
     * Get document type.
     *
     * Document type could be `Audio`, `Video`, `Sticker` and `Gif`.
     *
     * @return null|string document type if find it, `null` otherwise
     */
    public function getDocumentType(): ?string
    {
        $attributes = $this->update->get('message.media.document.attributes') ?? [];

        /**
         * @var \Arrayy\Arrayy $attribute
         */
        foreach ($attributes as $attribute) {
            $type = match ($attribute->get('_')) {
                'documentAttributeAudio'    => 'Audio',
                'documentAttributeVideo'    => 'Video',
                'documentAttributeSticker'  => 'Sticker',
                'documentAttributeAnimated' => 'Gif',
                default                     => '',
            };
            if ('' !== $type) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Get type of incoming message.
     *
     * Message type could be `Text`, `Photo`, `Audio`, `Video`, `Document`, `Contact`, `Gif`, `Sticker`,
     * `Poll`, `Geo`, `GeoLive`, `WebPage`, `Game`, `Venue`, `Invoice` and `Unsupported`
     *
     * @return null|string message type if find it, `null` otherwise
     */
    public function getMessageType(): ?string
    {
        $type = $this->getMediaType();
        if ('Empty' === $type || '' === $type) {
            $type = 'Text';
        }

        if ('Document' === $type && null !== $this->getDocumentType()) {
            $type = $this->getDocumentType();
        }

        return $type;
    }
}
