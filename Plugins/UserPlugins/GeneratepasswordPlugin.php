<?php

declare(strict_types=1);

namespace OxMohsen\Plugins\UserPlugins;

use OxMohsen\Plugins\UserPlugin;

final class GeneratepasswordPlugin extends UserPlugin
{
    /**
     * The name and signature of the plugin.
     *
     * @var string
     */
    protected $name = 'generatepassword';

    /**
     * The plugin description.
     *
     * @var string
     */
    protected $description = 'generate strong password';

    /**
     * The plugin regex pattern.
     *
     * @var string
     */
    protected $pattern = '/^[\!\#\.\/]generatepassword$/i';

    /**
     * The plugin usage.
     * This will help the user to find out how to use this plugin.
     *
     * @var string
     */
    protected $usage = '!generatepassword';

    /**
     * All characters that can be used for generate password.
     *
     * @var string[]
     */
    private $sets = ['abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', '0123456789', '!@#$%^&*'];

    public function execute(): \Generator
    {
        yield $this->MadelineProto->messages->sendMessage([
            'peer'            => $this->MadelineProto->update->getUpdate()->toArray(),
            'message'         => "🔑 Generated password is:<br><code>{$this->generatePassword()}</code>",
            'reply_to_msg_id' => $this->MadelineProto->update->getMessageId(),
            'parse_mode'      => 'HTML',
        ]);
    }

    /**
     * Generate password.
     *
     * @param int $length password length
     */
    private function generatePassword(int $length = 20): string
    {
        $all      = implode('', $this->sets);
        $allLen   = (int) mb_strlen($all, '8bit') - 1;
        $password = '';

        foreach ($this->sets as $set) {
            $setLen = (int) mb_strlen($set, '8bit') - 1;
            $password .= $set[random_int(0, $setLen)];
            --$length;
        }

        for ($i = 0; $i < $length; ++$i) {
            $password .= $all[random_int(0, $allLen)];
        }

        return str_shuffle($password);
    }
}
