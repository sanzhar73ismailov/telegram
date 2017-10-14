<?php
require_once "vendor/autoload.php";
require_once "includes/classes.php";
require_once "includes/functions.php";
require_once "includes/global.php";


define(LIST_EVENTS, "Список событий");
define(HELP, "Помощь");

$logfile = 'bot.log';
$cur_date_time = date("d/m/Y H:i:s");
$cur_date = date("d/m/Y");
$log_text = "\r\n*** Date: " . $cur_date . "\r\n";

try {

	$bot = new \TelegramBot\Api\BotApi(TOKEN);

	$keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(array(array(LIST_EVENTS, HELP)), false); // true for one-time keyboard
    $model = new Model();
	$messageInput = new InputMessage();
	$inputMessage_insert_id = $model->saveInputMessage($messageInput);
	
	$responseText = "Здравствуйте " . $messageInput->last_name . " " . $messageInput->first_name;
	
	switch ($messageInput->text) {
		case LIST_EVENTS:
			$service = new Service();
			$responseText .= "\nСписок событий на " . $cur_date;
			$responseText .= "\n" . $service->getKudagoEventsAsListText();
			break;
		case HELP:
			$responseText .= "\nЭто программа о событих и мероприятиях, проводимых в вашем городе.";
			break;
		default:
			$responseText .= "Нажмите на одну из кнопок внизу";
			break;
	}

	$log_text .= var_export($messageInput, true);
	$bot->sendMessage($messageInput->chat_id, $responseText , null, false, null, $keyboard);
	$model->saveOutputMessage($messageInput,$responseText,$inputMessage_insert_id);
	
} catch (\TelegramBot\Api\Exception $e) {
	$text .="exception: " . $e->getMessage();
}
file_put_contents($logfile, $text."\r\n", FILE_APPEND | LOCK_EX);

?>