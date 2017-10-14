<?php
include_once 'includes/config.php';

/**
 *
 * Класс сущность - мероприятия
 * @author javajan@mail.ru
 *
 */
class KudagoEvent {
	public $key;
	public $name;
}

/**
 *
 * Класс по работе с вебсервисами
 * @author admin
 *
 */
class Service{
	public static $urlForEvents = 'https://kudago.com/public-api/v1.3/event-categories/?lang=&order_by=&fields=';
	/**
	 *
	 * Получает список мероприятий по http
	 */
	public static function getKudagoEvents(){
		$arrayToReturn = array();
		$content = file_get_contents(self::$urlForEvents);
		$json = json_decode($content, true);
		foreach($json as $key => $val){
			//$reply .= ($key+1).". ". $val["name"]."\r\n";
			$event = new KudagoEvent();
			$event->key = $val["slug"];
			$event->name = $val["name"];
			$arrayToReturn[] = $event;
		}
		return $arrayToReturn;
	}

	public static function getKudagoEventsAsListText(){
		$arrEvents = self::getKudagoEvents();
		$str = "";
		$i = 0;
		foreach ($arrEvents as $event) {
			$str .= ++$i . ". " . $event->name . "\n";
		}
		return $str;
	}

}
/**
 *
 * Класс по получению сообщений от клиента Telagram
 * @author admin
 *
 */
class InputMessage {
	/*
	 * Пример получаемого jsonа
	 {
	 json view:
	 "update_id":1000079183,
	 "message":{
	 "message_id":157,
	 "from":{"id":431677371,"is_bot":false,"first_name":"Sanzhar","last_name":"Ismailov","language_code":"ru-RU"},
	 "chat":{"id":431677371,"first_name":"Sanzhar","last_name":"Ismailov","type":"private"},
	 "date":1507927009,
	 "text":"/start",
	 "entities":[{"offset":0,"length":6,"type":"bot_command"}]
	 }
	 }
	 * */
	public $update_id;
	public $chat_id;
	public $message_id;
	public $first_name;
	public $last_name;
	public $chat_type;
	public $date;
	public $text;

	function __construct () {
		$json = file_get_contents('php://input');
		$phpObjAssoc = json_decode($json, false);
		$this->update_id = $phpObjAssoc->update_id;
		$this->chat_id = $phpObjAssoc->message->chat->id;
		$this->message_id = $phpObjAssoc->message->message_id;
		$this->first_name = $phpObjAssoc->message->from->first_name;
		$this->last_name = $phpObjAssoc->message->from->last_name;
		$this->chat_type = $phpObjAssoc->message->chat->type;
		$this->date = $phpObjAssoc->message->date;
		$this->text = $phpObjAssoc->message->text;
	}

}
/**
 *
 * Класс по работе с базой данных
 * @author admin
 *
 */
class Model{

	private $connection;

	function __construct () {
		$servername = DB_HOST;
		$username = DB_USER;
		$password = DB_PASS;
		$dbname = DB_NAME;
		try {
			$this->connection = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$this->connection->query("SET NAMES 'utf8'");
		}
		catch(PDOException $e)
		{
			echo $sql . "<br>" . $e->getMessage();
		}
	}

	/**
	 *
	 * Сохранение исходящего сообщения
	 * @param $inputMessage
	 * return возвращает ID вставленной записи
	 */
	public function saveInputMessage($inputMessage){
		//$inputMessage = new InputMessage();
		$last_id = 0;
		try {
			// set the PDO error mode to exception
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connection->beginTransaction();
			$query = "INSERT INTO
						  message
						(
						  direction,
						  update_id,
						  chat_id,
						  message_id,
						  first_name,
						  last_name,
						  chat_type,
						  date_telegram,
						  text
						) 
						VALUE (
						  :direction,
						  :update_id,
						  :chat_id,
						  :message_id,
						  :first_name,
						  :last_name,
						  :chat_type,
						  :date_telegram,
						  :text
						)";
			$stmt = $this->connection->prepare($query);
			$stmt->bindValue(':direction', "in");
			$stmt->bindValue(':update_id', $inputMessage->update_id);
			$stmt->bindValue(':chat_id', $inputMessage->chat_id);
			$stmt->bindValue(':message_id', $inputMessage->message_id);
			$stmt->bindValue(':first_name', $inputMessage->first_name);
			$stmt->bindValue(':last_name', $inputMessage->last_name);
			$stmt->bindValue(':chat_type', $inputMessage->chat_type);
			$stmt->bindValue(':date_telegram', $inputMessage->date);
			$stmt->bindValue(':text', $inputMessage->text);
			$stmt->execute();
			$last_id = $this->connection->lastInsertId();
			$this->connection->commit();
			
		}
		catch(PDOException $e)		{
			$this->connection->rollback();
			echo $sql . "<br>" . $e->getMessage();
		}
		return $last_id;
	}

	/**
	 *
	 * Сохранение входящего сообщения
	 * @param $outputMessage
	 */
	public function saveOutputMessage($inputMessage,$outputText, $inputMessage_insert_id=""){
		$last_id = 0;
		try {
			// set the PDO error mode to exception
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connection->beginTransaction();
			$query = "INSERT INTO
						  message
						(
						  ref_id, 
						  direction,
						  update_id,
						  chat_id,
						  message_id,
						  text
						) 
						VALUE (
						  :ref_id,
						  :direction,
						  :update_id,
						  :chat_id,
						  :message_id,
						  :text
						)";
			$stmt = $this->connection->prepare($query);
			$stmt->bindValue(':ref_id', $inputMessage_insert_id);
			$stmt->bindValue(':direction', "out");
			$stmt->bindValue(':update_id', $inputMessage->update_id);
			$stmt->bindValue(':chat_id', $inputMessage->chat_id);
			$stmt->bindValue(':message_id', $inputMessage->message_id);
			//$stmt->bindParam(':first_name', $inputMessage->first_name);
			//$stmt->bindParam(':last_name', $inputMessage->last_name);
			//$stmt->bindParam(':chat_type', $inputMessage->chat_type);
			//$stmt->bindParam(':date_telegram', $inputMessage->date);
			$stmt->bindValue(':text', $outputText);
			$stmt->execute();
			$last_id = $this->connection->lastInsertId();
			$this->connection->commit();
			
		}
		catch(PDOException $e)		{
			$this->connection->rollback();
			echo $sql . "<br>" . $e->getMessage();
		}
		return $last_id;
	}

}