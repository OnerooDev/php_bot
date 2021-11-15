<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

/**
 * Rules command
 *
 * Gets executed when a user first starts using the bot.
 */
class BanCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'ban';

    /**
     * @var string
     */
    protected $description = 'rules command';

    /**
     * @var string
     */
    protected $usage = '/ban';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();

        $chat    = $message->getChat();
        $user    = $message->getFrom();
        $text    = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());
        $this->conversation->stop();

        $text = "Вы заблокированы!".PHP_EOL.PHP_EOL;

        $keyboard = new Keyboard(
			['Главная']
		);
		$keyboard->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
            'parse_mode'    => "MARKDOWN",
			'reply_markup' => $keyboard,
        ];

        return Request::sendMessage($data);
    }
}
