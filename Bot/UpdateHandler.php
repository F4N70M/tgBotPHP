<?php

/**
 * Получает запрос
 * На основе Конфигурации обрабатывает его
 * Выдает ответ
 * 
 * Необходимые данные:
 * - Update
 *   - User
 *   - Chat
 *     - private
 *     - group
 *     - supergroup
 *     - chanel
 *  
 * 
 */

namespace Bot;

use Bot\i\iUpdateHandler;

use Bot\i\iApi;
use Bot\i\iCore;
use Bot\i\Objects\iDataBase;
use Bot\Objects\Update;
use Bot\Objects\User;
// use Bot\Logger;

class UpdateHandler implements iUpdateHandler
{
	// protected $Api;
	// protected $DataBase;
	protected $Bot;
	protected $config;

	protected $User;

	protected $update;

	/**
	 *
	 */
	public function __construct(iCore $bot, array $config)
	{
		// $this->Api = $api;
		// $this->DataBase = $db;
		$this->Bot = $bot;
		$this->config = $config;
	}

	/**
	 *
	 */
	public function init(Update $update = null)
	{
		if ($update === null)
			$update = $this->getUpdate();

		if ($update)
		{
			$this->User = new User($this->Bot->DataBase, $update->getFrom());
			if ($this->User->get("nav") === null)
				$this->User->set(["nav"=>$this->config["navDefault"]]);


			echo "<pre>";
			print_r(($backtrace = debug_backtrace())[0]["class"]."::"
				.$backtrace[0]["function"]."()".PHP_EOL);
			// print_r($this->User);
			print_r($this->update);
			echo "</pre>";

			if ($update->isCallbackQuery())
			{}
			elseif ($update->isMessage())
			{
				// get bot commands
				// get bot aliases
				// get user nav
				// get user expect status
				// get user support mode status

				// is callback

				// is bot command
				if ( $this->isCommand($update->getText()) )
				{
					$command = $this->config["commands"][$update->getText()];
					$this->runCommand($command);
				}
				// is bot alias

				// is user nav menu command
				elseif ($this->isNavMenuCommand($update->getText(), $this->User->get("nav")))
				{
					$command = $this->getNavMenuCommand($update->getText(), $this->User->get("nav"));
					$this->runCommand($command);
				}
				// bot is expects data from user

				// support mode is online

				// default
				else
				{
					echo "<pre>";
					print_r(($backtrace = debug_backtrace())[0]["class"]."::"
						.$backtrace[0]["function"]."()".PHP_EOL);
					print_r("Event handler not found");
					echo "</pre>";
				}

			}

			// $command = $this->getCommand($update);
			// $operation = $this->convertCommandToOperation($command);
			// $statement = $this->prepare($operation);
			// $result = $statement->execute();
		}
	}

	/**
	 *
	 */
	public function getCommands()
	{
		return array_keys($this->config["commands"]);
	}

	/**
	 * 
	 */
	public function isCommand(string $command)
	{
		return in_array($command, $this->getCommands());
	}

	/**
	 * 
	 */
	public function isNavMenuCommand(string $command, string|null $nav)
	{

		return isset($this->config["nav"][$nav]["keyboard"]) &&
			is_array($this->config["nav"][$nav]["keyboard"]) &&
			array_key_exists($command, $this->config["nav"][$nav]["keyboard"]);
	}

	/**
	 * 
	 */
	public function getNavMenuCommand(string $key, string $nav)
	{
		return $this->config["nav"][$nav]["keyboard"][$key]["method"];
	}

	/**
	 * 
	 */
	public function runCommand(string $command)
	{
			echo "<pre>";
			print_r(($backtrace = debug_backtrace())[0]["class"]."::"
				.$backtrace[0]["function"]."()".PHP_EOL);
			print_r($command);
			echo "</pre>";
		// $doString = $this->config["commands"][$command];
		$doConfig = explode(":", $command);
		switch ($doConfig[0]) {
			case 'nav':
				$this->runNavMethod($doConfig[1]);
				break;

			case 'call':
				$this->runCallMethod($doConfig[1]);
				break;
			
			default:
				echo "<pre>";
				print_r(($backtrace = debug_backtrace())[0]["class"]."::"
					.$backtrace[0]["function"]."()".PHP_EOL);
				print_r($command);
				echo "</pre>";
				break;
		}
	}

	/**
	 *
	 */
	protected function runNavMethod(string $name)
	{
		// если значение относительное
		if (is_numeric($name))
		{
			//TODO: определить name раздела
		}

		// Если нет такого раздела то сбросить на дефолтный
		if (!$this->navExist($name))
		{
			$name = $this->config["navDefault"];
		}

		$config = $this->config["nav"][$name];
		$sendData = $this->getFormattedConfig($config);

		$result = $this->Bot->sendMessage($sendData);

		$this->Bot->Logger->push(($backtrace = debug_backtrace())[0]["class"]."::"
					.$backtrace[0]["function"]."()");
		$this->Bot->Logger->push("sendMessage");
		$this->Bot->Logger->push($result);

		if (isset($result["result"]))
		{
			$this->User->set(["nav" => $name]);
			return true;
		}

		return false;
	}

	/**
	 *
	 */
	protected function runCallMethod(string $name)
	{
		$config = $this->config["call"][$name];
		$sendData = $this->getFormattedConfig($config);

		$result = $this->Bot->sendMessage($sendData);

		$this->Bot->Logger->push(($backtrace = debug_backtrace())[0]["class"]."::"
					.$backtrace[0]["function"]."()");
		$this->Bot->Logger->push("sendMessage");
		$this->Bot->Logger->push($result);

		return isset($result["result"]);
	}

	/**
	 *
	 */
	public function getFormattedConfig(array $config)
	{
		$result = [];
		$result["chat_id"] = $this->User->getId();
		$result["text"] = $config["message"]["text"];

		if (
			isset($config["message"]["buttons"]) &&
			is_array($config["message"]["buttons"]) &&
			!empty($config["message"]["buttons"])
		)
		{
			$result["reply_markup"]["inline_keyboard"]
				= $config["message"]["buttons"];
		}
		elseif (
			isset($config["keyboard"]) &&
			is_array($config["keyboard"]) &&
			!empty($config["keyboard"])
		) {
			$keyboard = [];
			$i = $j = 0;
			foreach ($config["keyboard"] as $key => $value) {
				$keyboard[$j][$i]["text"] = $key;
				// Отправить Контакт
				if ($value["method"] == "request:contact")
					$keyboard[$j][$i]["request_contact"] = true;
				// Отправить Локацию
				if ($value["method"] == "request:location")
					$keyboard[$j][$i]["request_location"] = true;
				$i = abs($i-1);
				$j += $i==0?1:0;
			}
			$result["reply_markup"] = [
				'resize_keyboard' => true,
				'one_time_keyboard' => true,
				'keyboard' => $keyboard
			];
		}
		else
		{
			/*
			$result["reply_markup"] = [
				// 'resize_keyboard' => true,
				// 'one_time_keyboard' => true,
				"inline_keyboard" => [
					[
						["text"=>"test 1", "callback_data"=>"test"],
						["text"=>"test 2", "callback_data"=>"test"]
					],
					[
						["text"=>"test 3", "callback_data"=>"test"],
						["text"=>"test 4", "callback_data"=>"test"]
					]
				],
				// 'keyboard' => [
				// 	[["text"=>"test 1"],["text"=>"test 2"]],
				// 	[["text"=>"test 3"],["text"=>"test 4"]]
				// ]
			];
			*/

			// $result["reply_markup"] = null;
		}

		return $result;
	}

	/**
	 *
	 */
	public function navExist(string $name)
	{
		return array_key_exists($name, $this->config["nav"]);
	}

	/**
	 *
	 */
	public function getUpdate()
	{

		if ($this->update == null)
		{
			$data = json_decode(file_get_contents('php://input'), true);

			$this->update = is_array($data)
				? new Update($data)
				: false;

			// $this->update = $data
			// 	? $data
			// 	: false;
		}
		return $this->update;
	}
}