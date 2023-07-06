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
	public function sendMessage(
		int|string $chat_id,
		string $text,
		int $message_thread_id = null,
		string $parse_mode = null,
		// array $entities,
		// bool $disable_web_page_preview,
		bool $disable_notification = null,
		bool $protect_content = null,
		int $reply_to_message_id = null,
		bool $allow_sending_without_reply = null,
		array $reply_markup = null,
	)
	{
		$data = [
			"chat_id"	=> $chat_id,
			"text"		=> $text
		];
		foreach ([
			"message_thread_id",
			"parse_mode",
			// "entities",
			// "disable_web_page_preview",
			"disable_notification",
			"protect_content",
			"reply_to_message_id",
			"allow_sending_without_reply",
			"reply_markup"
		] as $key) {
			$value = $$key;
			if ($value === null) continue;
			$data[$key] = $value;
		}

		$this->call("sendMessage", $data);
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
		
		return (($return = json_decode($result, 1)) ? $return : $result);
	}
}

?>