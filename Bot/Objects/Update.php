<?php

namespace Bot\Objects;

use Bot\i\Objects\iUpdate;

class Update implements iUpdate
{

	protected $data;
	/**
	 *
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function getData(){
		return $this->data;
	}

	public function isMessage(){
		return isset($this->data["message"]) && isset($this->data["message"]["text"]);
	}

	public function isCallbackQuery(){
		return isset($this->data["callback_query"]) && isset($this->data["callback_query"]["data"]);
	}

	public function getText(){
		return $this->data["message"]["text"];
	}

	public function getFrom()
	{
		return $this->data[(
			$this->isMessage()
				? "message"
				: "callback_query"
		)]["from"];

	}

	public function getFromId()
	{
		return $this->getFrom()["id"];

	}
}