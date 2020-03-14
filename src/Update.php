<?php

declare(strict_types=1);

namespace MohsenJS;

abstract class Update
{
    /**
     * incoming update.
     *
     * @var array
     */
    protected $update = [];

    /**
     * Update contructor.
     *
     * @param array $update
     */
    public function __construct(array $update)
    {
        $this->update = $update;
    }

    /**
     * Get current update.
     *
     * @return array
     */
    public function getUpdate(): array
    {
        return $this->update;
    }

    /**
     * Get message text.
     *
     * @return string message text if find it, `empty string` otherwise.
     */
    public function getText(): string
    {
        return (string) ($this->update['message']['message'] ?? '');
    }

    /**
     * Get from id.
     *
     * @return int from id if find it, `zero` otherwise.
     */
    public function getFromId(): int
    {
        return (int) ($this->update['message']['from_id'] ?? 0);
    }

    /**
     * Get chat id.
     *
     * @return int chat id if find it, `zero` otherwise.
     */
    public function getChatId(): int
    {
        if (isset($this->update['message']['to_id']['_'])) {
            switch ($this->update['message']['to_id']['_']) {
                case 'peerChannel':
                    return (int) ('-100' . $this->update['message']['to_id']['channel_id'] ?? 0);
                case 'peerChat':
                    return (int) (-1 * $this->update['message']['to_id']['chat_id'] ?? 0);
                case 'peerUser':
                    return (int) ($this->update['message']['to_id']['user_id'] ?? 0);
            }
        }

        return 0;
    }

    /**
     * get message id.
     *
     * @return int message id if find it, `zero` otherwise.
     */
    public function getMessageId(): int
    {
        return (int) ($this->update['message']['id'] ?? 0);
    }

    /**
     * get reply message id.
     *
     * @return int reply message id if find it, `zero` otherwise.
     */
    public function getReplyMessageId(): int
    {
        return (int) ($this->update['message']['reply_to_msg_id'] ?? 0);
    }

    /**
     * Get media type.
     *
     * Media type could be `Empty`, `Photo`, `Geo`, `Contact`, `Unsupported`, `Document`, `WebPage`,
     * `Venue`, `Game`, `Invoice`, `GeoLive` and `Poll`.
     *
     * @return string|null media type if find it, `null` otherwise.
     */
    public function getMediaType(): ?string
    {
        if (isset($this->update['message']['media']['_'])) {
            $type = \substr($this->update['message']['media']['_'], 12);

            return $type !== false ? $type : null;
        }

        return null;
    }

    /**
     * Get document type.
     *
     * Document type could be `Audio`, `Video`, `Sticker` and `Gif`.
     *
     * @return string|null document type if find it, `null` otherwise.
     */
    public function getDocumentType(): ?string
    {
        if (
            ! isset($this->update['message']['media']['document']['attributes']) &&
            ! \is_array($this->update['message']['media']['document']['attributes'])
        ) {
            return null;
        }

        $type = [
            'documentAttributeAudio'    => 'Audio',
            'documentAttributeVideo'    => 'Video',
            'documentAttributeSticker'  => 'Sticker',
            'documentAttributeAnimated' => 'Gif',
        ];

        foreach ($this->update['message']['media']['document']['attributes'] as $attribute) {
            if (isset($attribute['_']) && isset($type[$attribute['_']])) {
                return $type[$attribute['_']];
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
     * @return string|null message type if find it, `null` otherwise.
     */
    public function getMessageType(): ?string
    {
        $type = $this->getMediaType();
        if ($type === 'Empty' || $type === null) {
            $type = 'Text';
        }

        if ($type === 'Document' && $this->getDocumentType() !== null) {
            $type = $this->getDocumentType();
        }

        return $type;
    }
}
