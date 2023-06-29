<?php

namespace Bot\Modules;

use Bot\i\Modules\iEventHandler;

use Bot\i\iApi;
use Bot\i\Objects\iDataBase;
use Bot\Objects\Update;
// use Bot\Logger;

class EventHandler implements iEventHandler
{
	protected $Api;
	protected $DataBase;
	protected $config;

	protected $update;

	/**
	 *
	 */
	public function __construct(iApi $Api, iDataBase $db, array $config)
	{
		$this->Api = $Api;
		$this->DataBase = $db;
		$this->config = $config;
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
					"text" => "2",
				],
			];
			// /TEST

			// $this->update = is_array($data)
			// 	? new Update($data)
			// 	: false;

			$this->update = $data
				? $data
				: false;
		}
		return $this->update;
	}
}