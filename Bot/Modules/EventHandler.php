<?php

namespace Bot\Modules;

use Bot\i\Modules\iEventHandler;

use Bot\i\iApi;
use Bot\i\Objects\iDataBase;
use Bot\Objects\Update;
use Bot\Objects\User;
// use Bot\Logger;

class EventHandler implements iEventHandler
{
	protected $Api;
	protected $DataBase;
	protected $config;
	protected $User;

	protected $update;

	/**
	 *
	 */
	public function __construct(iApi $api, iDataBase $db, array $config)
	{
		$this->Api = $api;
		$this->DataBase = $db;
		$this->config = $config;
	}

	/**
	 *
	 */
	public function init()
	{
		$update = $this->getUpdate();

		if ($update)
		{

			$this->User = new User($this->DataBase, $update->getFrom());

			echo "<pre>";
			print_r(($backtrace = debug_backtrace())[0]["class"]."::"
				.$backtrace[0]["function"]."()".PHP_EOL);
			print_r($this->update);
			echo "</pre>";

			if ($update->isMessage())
			{
				// get bot commands
				$commands = $this->getCommands();
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

			// if ($update->isCallbackQuery())
			// {
			// 	//
			// }

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
	public function isNavMenuCommand(string $command, string $nav)
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
		// Если нет такого раздела то сбросить на дефолтный
		if (!$this->navExist($name))
		{
			$name = $this->config["navDefault"];
		}

		/*
		$this->Api->sendMessage(
			chat_id	:	$this->User->getId(),
			text	:	"test",
			reply_markup	: [
				// 'resize_keyboard' => true,
				// 'one_time_keyboard' => false,
				'one_time_keyboard' => true,
				// 'keyboard' => [
				// 	[["text" => 1],["text" => 2]],
				// 	[["text" => 3],["text" => 4]],
				// 	[["text" => 5],["text" => 6]]
				// ]
				'inline_keyboard' => [
					[
						["text"=>"url", "url"=>"https://core.telegram.org/bots/api#keyboardbutton"],
						["text"=>"max ".($max=64), "callback_data"=>str_repeat("0123", round($max/4))]
					]
				]
			]
		);
		*/

		$config = $this->config["nav"][$name];
		$formattedConfig = $this->getFormattedConfig($config);

		$this->Api->sendMessage(
			chat_id			: $formattedConfig["chat_id"],
			text			: $formattedConfig["text"],
			reply_markup	: $formattedConfig["reply_markup"]
		);


		$this->User->set(["nav" => $name]);

	}

	/**
	 *
	 */
	protected function runCallMethod(string $name)
	{
		$config = $this->config["call"][$name];
		$formattedConfig = $this->getFormattedConfig($config);

		$this->Api->sendMessage(
			chat_id			: $formattedConfig["chat_id"],
			text			: $formattedConfig["text"],
			reply_markup	: $formattedConfig["reply_markup"]
		);
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
				'one_time_keyboard' => false,
				'keyboard' => $keyboard
			];
		}
		else
		{
			$result["reply_markup"] = null;
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


			// TEST
			$data = [
				"update_id" => 167621668,
				"message" => [
					"message_id" => 792,
					"from" => [
						"id" => 440955330,
						"is_bot" => false,
						"first_name" => "KONARD",
						"username" => "konard",
						"language_code" => "ru",
					],

					"chat" => [
						"id" => 440955330,
						"first_name" => "KONARD",
						"username" => "konard",
						"type" => "private",
					],

					"date" => 1687987651,
					"text" => "/start",
					// "text" => "💬  About",
				],
			];
			// /TEST

			// $this->updateData = $data;

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