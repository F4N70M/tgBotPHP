<?php

namespace Bot;

use Bot\i\iApi;

class Api implements iApi
{
	protected $token;
	protected $update;
	public $logger;

	/**
	 *
	 */
	public function __construct(string $token)
	{
		$this->token = $token;
		$this->logger = new Logger(dirname(__DIR__)."/bot.log");
	}


	/**
	 *
	 */
	// public function setWebhook(string $url)
	// {
	// 	$setWebhookUrl = "https://api.telegram.org/bot{$this->token}/setwebhook?url={$url}";
	// 	return $setWebhookUrl;
	// }


	/**
	 *
	 */
	// public function getUpdate()
	// {

	// 	if ($this->update == null)
	// 	{
	// 		$update = json_decode(file_get_contents('php://input'), true);

	// 		$this->update = $update ? $update : false;
	// 	}
	// 	return $this->update;
	// }



	/**
	 *
	 */
	public function call(string $method, array $data = [])
	{
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'https://api.telegram.org/bot' . $this->token . '/' . $method,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"))
		]);
		$result = curl_exec($curl);
		curl_close($curl);
		
		return (json_decode($result, 1) ? json_decode($result, 1) : $result);
	}
}

?>