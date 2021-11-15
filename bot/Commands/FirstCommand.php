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

require_once "../lib/config_class.php";

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class FirstCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'first';

    /**
     * @var string
     */
    protected $description = 'Команда начало';

    /**
     * @var string
     */
    protected $usage = '/first';

     /**
     * @var string
     */
    protected $version = '1.1.0';

     /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

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
		$ref_id = trim($text);
		
		$this->config = new \Config();
		
		$mysqli = new \mysqli($this->config->host, $this->config->user, $this->config->password, $this->config->db);
        $mysqli->query("SET NAMES 'utf8'");
				
		//
        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());
        $this->conversation->stop();
        
        if(isset($user->first_name) && !empty($user->first_name)) $name = $user->first_name;
        else $name = $user->username;
        
		$text    = "$name, Добро пожаловать в бота поддержки " . PHP_EOL;
        $text    .= "<b>Сделайте выбор в меню</b>".PHP_EOL;
        
        $keyboard = new Keyboard(
			['Создать запрос'],
			['Личный кабинет']
		);
		$keyboard->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
            'parse_mode'    => "HTML",
			'reply_markup' => $keyboard,
        ];
        return Request::sendMessage($data);
    }
}
