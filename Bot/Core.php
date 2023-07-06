<?php

namespace Bot;

use Bot\i\iCore;
use Bot\Objects\DataBase;
use Bot\Modules\EventHandler;

/**
 * 
 */
class Core implements iCore
{
	protected $Api;
	protected $DataBase;

	public $EventHandler;
	public $Logger;
	
	public function __construct(string $token, array $config = [])
	{
		$this->Logger = new Logger(__DIR__."/log.txt");

		$this->Api = new Api($token);

		$connection = new \Bot\Objects\DataBaseConnection("localhost", "root", "root", "tg_main");
		$dbLink = $connection->get();
		$this->DataBase = new DataBase($dbLink);

		$this->EventHandler = new EventHandler(
			$this->Api,
			$this->DataBase,
			$config
		);

		// $this->init($config);
	}

	/**
	 * Отправить сообщение
	 */
	public function sendMessage(array $data)
	{
		return $this->Api->call('sendMessage', $data);
	}

	/**
	 * Обработать обновление
	 */
	public function processUpdate(){}

	/**
	 * Получить инфо о боте
	 */
	public function getMe()
	{
		return $this->Api->call("getMe")["result"];
	}

	/**
	 * Получить конфигурацию Webhook Tg Bot API
	 */
	public function getWebhookInfo()
	{
		return $this->Api->call("getWebhookInfo")["result"];
	}
}