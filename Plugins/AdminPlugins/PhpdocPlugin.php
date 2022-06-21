<?php

declare(strict_types=1);

namespace OxMohsen\Plugins\AdminPlugins;

use Amp\File;
use OxMohsen\Config;
use OxMohsen\Plugins\AdminPlugin;

final class PhpdocPlugin extends AdminPlugin
{
    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'phpdoc';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'read the php documentation for an object, class, constant, method or property.';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]phpdoc ([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!phpdoc "target"';

    /**
     * If this plugin is enabled.
     *
     * @var bool
     */
    protected $enabled = true;

    public function execute(): \Generator
    {
        $fileName    = Config::DATA_PATH.uniqid('tmp_'.mt_rand(0, 100).'_');
        $fileHandler = yield File\openFile($fileName, 'c+');
        $fileHandler->write($this->getPhpDoc());
        $fileHandler->close();

        yield $this->MadelineProto->messages->sendMedia([
            'peer'  => $this->MadelineProto->update->getUpdate()->toArray(),
            'media' => [
                '_'          => 'inputMediaUploadedDocument',
                'file'       => $fileName,
                'mime_type'  => 'text/plain',
                'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => "{$this->getMatches()[1]}.html"]],
            ],
            'message'         => '',
            'reply_to_msg_id' => $this->MadelineProto->update->getMessageId(),
        ]);

        yield File\deleteFile($fileName);
    }

    /**
     * Find and return php document.
     */
    private function getPhpDoc(): string
    {
        $database = new \PDO('sqlite:'.__DIR__.DIRECTORY_SEPARATOR.'php_manual.sqlite');
        $sth      = $database->query("SELECT `doc` FROM `php_manual` WHERE `id`='{$this->getMatches()[1]}'");
        if (false !== $sth) {
            $doc = $sth->fetchColumn();

            return false !== $doc ? nl2br((string) $doc) : '404 Not found!';
        }

        return 'An error occurred :(';
    }
}
