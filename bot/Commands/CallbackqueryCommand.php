<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Longman\TelegramBot\Request;

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
    public function execute() {
      //принимам входящее сообщение
      $callback_query    = $this->getCallbackQuery();
      $callback_query_id = $callback_query->getId();
      $callback_data     = $callback_query->getData();

  		$message = $callback_query->getMessage();
  		$user    = $callback_query->getFrom();
  		$chat_id = $message->getChat()->getId();
  		$user_id = $user->getId();
      //подгружаем данные из конфигурации
  		$this->config = new \Config();
      //подключаем бд
  		$mysqli = new \mysqli($this->config->host, $this->config->user, $this->config->password, $this->config->db);
      $mysqli->query("SET NAMES 'utf8'");

      $explode = explode(":", $callback_data);

//get_hello Begin|Начало
		  if($explode[0] == 'get_hello'){
        //извлекаем данные из бд
          $query = "SELECT * FROM `user` WHERE `id` = '".$user_id."'";
          $user_query = $mysqli->query($query);
          $user_array = $user_query->fetch_array();
        //удаляем старое сообщение
  				$message_to_edit = $message->getMessageId();
  				$data_edit = [
  					'chat_id'    => $chat_id,
  					'message_id' => $message_to_edit,
  				];
  				Request::deleteMessage($data_edit);

  				$text .= 'Ну привет '.$user_array['first_name'].PHP_EOL;

          $inline_keyboard = new InlineKeyboard([

            new InlineKeyboardButton([
              'text'  => '🗓Таблица',
              'callback_data'     => 'get_Worktable'
            ])],
            [new InlineKeyboardButton([
              'text'  => '📋Задачи',
              'callback_data' => 'go_to_task'
            ]),
            new InlineKeyboardButton([
              'text'  => '📄3logicWiki',
              'url' => 'http://wiki.3l.host'
            ]),
            new InlineKeyboardButton([
              'text'  => '☎️Контакты',
              'callback_data' => 'get_Contacts'
            ])],
            [new InlineKeyboardButton([
              'text'  => '↩️Назад',
              'callback_data'	=> 'get_back:'
            ])
          ]);
        //вносим необходимые данные в массив отправляемого сообщения
          $datas['text'] = $text;
          $datas['parse_mode'] = "MARKDOWN";
          $datas['chat_id'] = $chat_id;
          $datas['reply_markup'] = $inline_keyboard;

          return Request::sendMessage($datas);
      }
//get_hello end|Конец
//get_back Begin|Начало
      if($explode[0] == 'get_back'){
        //удаляем старое сообщение
  				$message_to_edit = $message->getMessageId();
  				$data_edit = [
  					'chat_id'    => $chat_id,
  					'message_id' => $message_to_edit,
  				];
  				Request::deleteMessage($data_edit);
        //
          $text = "Добро пожаловать в бота 3Logic ".PHP_EOL;
          $text .= "✅Сделайте выбор в меню✅".PHP_EOL;

          $inline_keyboard = new InlineKeyboard([
        		new InlineKeyboardButton([
        			'text'  => 'Меню',
        			'callback_data'	=> 'get_hello:'
/**        		])],
*            [new InlineKeyboardButton([
*              'text'  =>  'Администратор',
*              'callback_data' =>  'get_item'
*            ])
**/      		])]);
          //вносим необходимые данные в массив отправляемого сообщения
            $datas['text'] = $text;
            $datas['parse_mode'] = "MARKDOWN";
            $datas['chat_id'] = $chat_id;
            $datas['reply_markup'] = $inline_keyboard;

            return Request::sendMessage($datas);
        }
//get_back end|Конец
//get_Contacts Begin|Начало
      if($explode[0] == 'get_Contacts'){
        $query = "SELECT * FROM `Contacts`";
        $all_contacts = $mysqli->query($query);
        $contacts_array = $all_contacts->fetch_array();
  //удаляем старое сообщение
         $message_to_edit = $message->getMessageId();
         $data_edit = [
            'chat_id'    => $chat_id,
            'message_id' => $message_to_edit,
          ];
          Request::deleteMessage($data_edit);
        //
          $text = "Контакты:".PHP_EOL;
        //  $text .= "<b>Сделайте выбор</b>".PHP_EOL;

        foreach ($all_contacts as $value) {
        $text .= $value ['name'] ."\n"
        ."Должность:" ." " .$value ['position'] ."\n"
        ."Номер:" ." " .$value ['number'] ."\n"
//        ."Почта:" ." " .$vaule ['email'] ."\n"
        ."День Рождения:" ." " .$value ['birthday'] ."\n"
        .PHP_EOL;
        }

          $inline_keyboard = new InlineKeyboard([
/**        		new InlineKeyboardButton([
*        			'text'  => 'Инженерный отдел',
*        			'callback_data'	=> 'get_Contacts'
*        		])],
*            [new InlineKeyboardButton([
*              'text'  =>  'Отдел Сборка',
*              'callback_data' =>  'get_Contacts'
*            ])],
*/            new InlineKeyboardButton([
              'text' => '↩️Назад',
              'callback_data' => 'get_hello:'
            ])
      		]);
        //вносим необходимые данные в массив отправляемого сообщения
          $datas['text'] = $text;
          $datas['parse_mode'] = "MARKDOWN";
          $datas['chat_id'] = $chat_id;
          $datas['reply_markup'] = $inline_keyboard;

          return Request::sendMessage($datas);
      };
//get_Contacts end|Конец
//get_Worktable Begin|Начало
      if($explode[0] == 'get_Worktable'){
        $query = "SELECT * FROM `Worktable`";
        $all_worktable = $mysqli->query($query);
        $worktable_array = $all_worktable->fetch_array();
  //удаляем старое сообщение
         $message_to_edit = $message->getMessageId();
         $data_edit = [
            'chat_id'    => $chat_id,
            'message_id' => $message_to_edit,
          ];
          Request::deleteMessage($data_edit);
        //
          $text = "Таблица:".PHP_EOL;
        //  $text .= "<b>Сделайте выбор</b>".PHP_EOL;
/**
*        foreach ($all_worktable as $value) {
*        $text .=
*        "Дата поездки:" ." " .$value ['Travel_date'] ."\n"
*        ."Наиминование Заказчика:" ." " .$value ['Customer_name'] ."\n"
*        ."Адрес Заказчика:" ." " .$value ['Customer_address'] ."\n"
*        ."Контакты Заказчика:" ." " .$vaule ['Customer_contacts'] ."\n"
*        ."Цель поездки или номер инцидента:" ." " .$value ['Purpose_of_trip_or_incident_number'] ."\n"
*        ."Наиминование и S/N оборудования:" ." " .$value ['Equipment_name_and_S/N'] ."\n"
*        ."Количество:" ." " .$value ['Quantity'] ."\n"
*        ."ФИО инженера:" ." " .$value ['Full_name_of_engineer'] ."\n"
*        .PHP_EOL;
*        }
*/
          $inline_keyboard = new InlineKeyboard([
        		new InlineKeyboardButton([
        			'text'  => 'Google Таблица',
        			'url'	=> 'https://docs.google.com/spreadsheets/d/1wQItHbu8jINUPGAiNtZ5nesTaftEL3elmEfKXE5-M8w/edit?usp=sharing'
        		])],
/**            [new InlineKeyboardButton([
*              'text'  =>  'Редактировать',
*              'callback_data' =>  'get_Worktable'
*            ])],
*/            [new InlineKeyboardButton([
              'text' => '↩️Назад',
              'callback_data' => 'get_hello:'
            ])
      		]);

        //вносим необходимые данные в массив отправляемого сообщения
          $datas['text'] = $text;
          $datas['parse_mode'] = "MARKDOWN";
          $datas['chat_id'] = $chat_id;
          $datas['reply_markup'] = $inline_keyboard;

          return Request::sendMessage($datas);
      };
//get_Worktable end|Конец
//get_3lwiki Begin|Начало
      if($explode[0] == 'get_3lWiki'){
  //удаляем старое сообщение
         $message_to_edit = $message->getMessageId();
         $data_edit = [
            'chat_id'    => $chat_id,
            'message_id' => $message_to_edit,
          ];
          Request::deleteMessage($data_edit);
        //
          $text = "3logicWiki".PHP_EOL;
        //  $text .= "<b>Сделайте выбор</b>".PHP_EOL;

          $inline_keyboard = new InlineKeyboard([
        		new InlineKeyboardButton([
        			'text'  => '3logicWiki',
        			'callback_data'	=> 'get_3lWiki'
        		])],
/**            [new InlineKeyboardButton([
*              'text'  =>  '',
*              'callback_data' =>  ''
*            ])],
*/            [new InlineKeyboardButton([
              'text' => '↩️Назад',
              'callback_data' => 'get_hello:'
            ])
      		]);

        //вносим необходимые данные в массив отправляемого сообщения
          $datas['text'] = $text;
          $datas['parse_mode'] = "MARKDOWN";
          $datas['chat_id'] = $chat_id;
          $datas['reply_markup'] = $inline_keyboard;

          return Request::sendMessage($datas);
      };
//get_3lwiki end|Конец
//go_to_task Begin|Начало
      if($explode[0] == 'go_to_task'){
         $message_to_edit = $message->getMessageId();
         $data_edit = [
            'chat_id'    => $chat_id,
            'message_id' => $message_to_edit,
          ];
          Request::deleteMessage($data_edit);

          $text = "Создай меня :)".PHP_EOL;

          $inline_keyboard = new InlineKeyboard([
        		new InlineKeyboardButton([
        			'text'  => '🖍Создать Задачу',
        			'callback_data'	=> 'Create_a_task'
        		])],
            [new InlineKeyboardButton([
              'text'  =>  '📋🗓Мои Сегодня',
              'callback_data' =>  'get_Task'
            ]),
            new InlineKeyboardButton([
              'text'  =>  '📋🗓Мои Все',
              'callback_data' =>  'get_Task_All'
            ])],
            [new InlineKeyboardButton([
              'text' => '↩️Назад',
              'callback_data' => 'get_hello:'
            ])
      		]);

          $datas['text'] = $text;
          $datas['parse_mode'] = "MARKDOWN";
          $datas['chat_id'] = $chat_id;
          $datas['reply_markup'] = $inline_keyboard;

          return Request::sendMessage($datas);
      };
//end|Конец
//Menu Create_a_task начало
          if($explode[0] == 'Create_a_task'){
            $query = "SELECT * FROM `user` WHERE `id` = '".$user_id."'";
            $user_query = $mysqli->query($query);
            $user_array = $user_query->fetch_array();
          $message_to_edit = $message->getMessageId();
          $data_edit = [
            'chat_id'    => $chat_id,
            'message_id' => $message_to_edit,
          ];
          Request::deleteMessage($data_edit);
          $text = "Создание новой задачи.".PHP_EOL;
          $text .= "После заполнения нажмите кнопку Создать.".PHP_EOL;
          $text .= "Необходимое поле - Заголовок.".PHP_EOL;
          $text .= "🔸Заголовок:".PHP_EOL;
          $text .= "🔸Чат:".PHP_EOL;
          $text .= '🔸Исполнитель: '.$user_array['first_name'].PHP_EOL;
          $text .= "🔸Срок:".PHP_EOL;
          $text .= "📜Описание:".PHP_EOL;
          $inline_keyboard = new InlineKeyboard([
        		new InlineKeyboardButton([
        			'text'  => '🟠Заголовок',
        			'callback_data'	=> 'get_header'
            ])],
              [new InlineKeyboardButton([
                'text'  =>  '🟠Описание',
                'callback_data' =>  'get_description'
              ]),
              new InlineKeyboardButton([
                'text'  =>  '🟠Срок',
                'callback_data' =>  'get_item'
              ])],
              [new InlineKeyboardButton([
                'text'  =>  '🟠Чат',
                'callback_data' =>  'get_group'
              ]),
              new InlineKeyboardButton([
                'text'  =>  '🟠Исполнитель',
                'callback_data' =>  'get_item'
              ])],
              [new InlineKeyboardButton([
                'text'  =>  '✅Создать',
                'callback_data' =>  'get_item'
              ])],
              [new InlineKeyboardButton([
                'text' => '↩️Назад',
                'callback_data' => 'go_to_task:'
              ]),
              new InlineKeyboardButton([
                'text'  =>  '🗑Очистить',
                'callback_data' =>  'get_item'
              ])]);

              $datas['text'] = $text;
              $datas['parse_mode'] = "MARKDOWN";
              $datas['chat_id'] = $chat_id;
              $datas['reply_markup'] = $inline_keyboard;

              return Request::sendMessage($datas);
          };
//Menu Create_a_task end|Конец
//button get_header Begin|Начало
          if($explode[0] == 'get_header'){
          $query = "SELECT * FROM `Create_Task`";
          $task_Table = $mysqli->query($query);
          $tasktable_array = $task_Table->fetch_array();
          $message_to_edit = $message->getMessageId();
          $data_edit = [
            'chat_id'    => $chat_id,
            'message_id' => $message_to_edit,
          ];
          Request::deleteMessage($data_edit);
          $text = "Отправьте заголовок:".PHP_EOL;






          $datas['text'] = $text;
          $datas['parse_mode'] = "MARKDOWN";
          $datas['chat_id'] = $chat_id;
          $datas['reply_markup'] = $inline_keyboard;

          return Request::sendMessage($datas);
      };
//button get_header end|Конец
//button get_description Begin|Начало
          if($explode[0] == 'get_description'){
          $query = "SELECT * FROM `Create_Task`";
          $task_Table = $mysqli->query($query);
          $tasktable_array = $task_Table->fetch_array();
          $message_to_edit = $message->getMessageId();
          $data_edit = [
            'chat_id'    => $chat_id,
            'message_id' => $message_to_edit,
          ];
          Request::deleteMessage($data_edit);
          $text = "Отправьте описание:".PHP_EOL;






          $datas['text'] = $text;
          $datas['parse_mode'] = "MARKDOWN";
          $datas['chat_id'] = $chat_id;
          $datas['reply_markup'] = $inline_keyboard;

          return Request::sendMessage($datas);
        };
//button get_description end|Конец
//button get_group Begin|Начало
          if($explode[0] == 'get_group'){

          $message_to_edit = $message->getMessageId();
          $data_edit = [
            'chat_id'    => $chat_id,
            'message_id' => $message_to_edit,
          ];
          Request::deleteMessage($data_edit);
          $text = "Выберите чат:".PHP_EOL;






          $datas['text'] = $text;
          $datas['parse_mode'] = "MARKDOWN";
          $datas['chat_id'] = $chat_id;
          $datas['reply_markup'] = $inline_keyboard;

          return Request::sendMessage($datas);
        };
//button get_group end|Конец
        $data = [
  			'chat_id'      => $chat_id,
  			'parse_mode'   => 'MARKDOWN',
  			'text'         => $text,
  			'reply_markup' => $inline_keyboard,
  		];

  		return Request::sendMessage($data);
    }

}



// Заметки
            //$inline_keyboard->addRow(
            //Масив, пример $a = AddRow(1, 2, 3, 4, 5); print_f($a);
            // будет отображена переменная а
            //[1, 2, 3, 4, 5]

/**            new InlineKeyboardButton([
*              'text'  => 'Чат',
*              'url'     => 'https://t.me/joinchat/IDrw6HcS-ak0ZmEy'
*            ]),
*/
//            new InlineKeyboardButton([
//              'text'  => 'Item',
//              'callback_data'	=> 'get_item:'
//            ]),
