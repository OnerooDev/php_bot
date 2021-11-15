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

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;

require_once "../lib/config_class.php";

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Генерация команды';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

	/**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

    /**
     * Command execute method if MySQL is required but not available
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function executeNoDb()
    {
        // Do nothing
        return Request::emptyResponse();
    }

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
		$message = $this->getMessage();
		$from    = $message->getFrom();
		$chat = $message->getChat();
		$chat_id = $chat->getId();
        $user_id = $from->getId();
        $this->config = new \Config();

        //If a conversation is busy, execute the conversation command after handling the message
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );

        $mysqli = new \mysqli($this->config->host, $this->config->user, $this->config->password, $this->config->db);
        $mysqli->query("SET NAMES 'utf8'");
        $result_set = $mysqli->query("SELECT * FROM `user` WHERE `id` = $user_id");
        $row = $result_set->fetch_assoc();
        $admid = $this->config->adminids;

        if($row['status'] == 0){
            return $this->telegram->executeCommand('ban');
        }
		if(trim($message->getText()) == "Главная"){
			return $this->telegram->executeCommand('start');
		}
		elseif(trim($message->getText()) == 'Вернуться'){
			return $this->telegram->executeCommand('start');
		}
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command);
        }
		else{
			$keyboard = new Keyboard(
				["Главная"]
			);
			$keyboard->setResizeKeyboard(true)
				->setOneTimeKeyboard(true)
				->setSelective(false);
		   $data = [
				'chat_id' => $chat_id,
				'parse_mode' => 'html',
				'text'    => 'Я Вас не понимаю, попробуйте снова',
				'reply_markup' => $keyboard
			];

			return Request::sendMessage($data);
		};
    }
}
