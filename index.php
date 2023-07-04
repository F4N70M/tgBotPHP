<?php
/**
 * aka Event Handler
 */


// Объявить константу корневой директории проекта
define("ROOT_DIR", str_replace('\\', '/', __DIR__));

$config = json_decode(file_get_contents(ROOT_DIR."/config.json"), true);


if (($config["php"]["errors"]??false))
{
	// Добавлять в отчет все PHP ошибки
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}


// API token
$token = $config["api"]["token"];
// Bot configuration
$botConfig = $config["bot"];

// Подключить автоподгрузчик класссов
require_once(ROOT_DIR."/autoload.php");
// Подключить бота
$Bot = new Bot\Core($token, $botConfig);


// Инициализировать Обработчик обновлений
$Bot->EventHandler->init();

// // Получить обновление
// $update = $Bot->EventHandler->getUpdate();

// if ($update) 
// {
// 	echo "<code style=\"white-space: pre;\">";
// 	print_r($update);
// 	echo "</code>";

	

// 	exit("exit");
// }



// $command = $Bot->EventHandler->getCommand($update);
// $operation = $Bot->EventHandler->convertCommandToOperation($command);
// $statement = $Bot->EventHandler->prepare($operation);
// $result = $statement->execute();


// try {

// 	// Информация о боте
// 	// $Bot->Logger->push($Bot->getMe());

// 	// $commands = $Bot->call("getMyCommands", []);
// 	// pre($commands);
// 	// pre($Bot->getWebhookInfo()["url"]);


// 	// Получить данные обновления
// 	// $update = $Bot->getUpdate();
// 	// $Bot->Logger->push($update);
	
// 	if ($update)
// 	{
// 		$chatId = $update->chat["id"];
// 		// $chatId = $update["callback_query"]["from"]["id"]??$update["message"]["from"]["id"];

// 		$data = [
// 			"chat_id"	=> $chatId,
// 			// "text"		=> "lolkek"
// 			"text"		=> print_r($update,true)
// 		];
// 		$result = $Bot->sendMessage($data);
// 		// $data = [
// 		// 	"chat_id"	=> $chatId,
// 		// 	// "text"		=> "lolkek"
// 		// 	"text"		=> print_r($result,true)
// 		// ];
// 		// $result = $Bot->sendMessage($data);
// 		$Bot->Logger->push($data);


// 		// $API->
// 	}
	
// } catch (Exception $e) {
// 	print_r($e);
// }

