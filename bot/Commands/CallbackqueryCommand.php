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
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Longman\TelegramBot\Request;

require_once "../module/coinbase.php";
require_once "../bot/vendor/easybtc.php";

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlinekeyboardCommand.php
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '1.1.1';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $callback_query    = $this->getCallbackQuery();
        $callback_query_id = $callback_query->getId();
        $callback_data     = $callback_query->getData();

		$message = $callback_query->getMessage();
		$user    = $callback_query->getFrom();
		$chat_id = $message->getChat()->getId();
		$user_id = $user->getId();
		$this->config = new \Config();
		$mysqli = new \mysqli($this->config->host, $this->config->user, $this->config->password, $this->config->db);
        $mysqli->query("SET NAMES 'utf8'");

		if($explode[0] == 'get_area'){

			$area = $explode[1];
			$query = "SELECT * FROM `catalog` WHERE `area` = '".$area."'";
			$qarea = $mysqli->query($query);
			$qarea = $qarea->fetch_array();
			$check = 1;
			if($check === 1){

				$message_to_edit = $message->getMessageId();
				$data_edit = [
					'chat_id'    => $chat_id,
					'message_id' => $message_to_edit,
				];

				Request::deleteMessage($data_edit);

				$text .= 'Доступные позиции ⬇️'.$id_trans.PHP_EOL;
				$datas['text'] = $text;
				$datas['parse_mode'] = "MARKDOWN";
				$datas['chat_id'] = $chat_id;
				$inline_keyboard = new InlineKeyboard();
                $inline_keyboard->addRow(new InlineKeyboardButton([
                            'text'  => 'Item - 1',
                            'callback_data'	=> 'get_item:1'
                        ]),
                        new InlineKeyboardButton([
                            'text'  => 'Item - 2',
                            'callback_data'	=> 'get_item:2'
                        ]));
                $datas['reply_markup'] = $inline_keyboard;
				return Request::sendMessage($datas);
			}
			else{
				$temp_data = [
					'callback_query_id' => $callback_query_id,
					'text'              => 'Нет доступных позиций',
					'show_alert'        => $callback_data === 'thumb up',
					'cache_time'        => 5,
				];

				return Request::answerCallbackQuery($temp_data);
				exit();
			}
		}
		

        $data = [
			'chat_id'      => $chat_id,
			'parse_mode'   => 'MARKDOWN',
			'text'         => $text,
			'reply_markup' => $inline_keyboard,
		];

		return Request::sendMessage($data);
    	}

}
