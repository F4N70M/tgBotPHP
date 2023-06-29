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
	public $Logger;
	
	public function __construct(string $token, array $config = [])
	{
		$this->Logger = new Logger(__DIR__."/log.txt");

		$this->Api = new Api($token);

		$this->DataBase = new DataBase();

		$this->EventHandler = new EventHandler(
			$this->Api,
			$this->DataBase,
			$config
		);

		// $this->init($config);
	}

	/**
	 * Инициализировать конфигурацию бота
	 */
	public function init(array $config)
	{
		#
	}

	/**
	 * Получить обновление
	 */
	// public function getUpdate()
	// {
	// 	return $this->Api->getUpdate();
	// }

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

	public function getWebhookInfo()
	{
		return $this->Api->call("getWebhookInfo")["result"];
	}
}