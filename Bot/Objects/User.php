<?php

namespace Bot\Objects;

use Bot\i\Objects\iUser;
use Bot\i\Objects\iDataBase;

class User implements iUser
{
	protected $DataBase;

	protected $id;
	protected $data;

	/**
	 *
	 */
	public function __construct(iDataBase $DataBase, int|array $id_data)
	{
		$this->DataBase = $DataBase;

		$this->id = is_int($id_data) ? $id_data : $id_data["id"];

		if (!$this->exists())
		{
			// $this->DataBase->select()->from()
			$this->create(is_array($id_data) ? $id_data : ["id"=>$id_data]);
		}
	}
	
	/**
	 *
	 */
	public function get(string $key = null)
	{
		if (!$this->data)
		{
			$this->data = 
				($result = $this->DataBase->getUser($this->id))
					? $result[0]
					: false;
		}
		if ($key !== null)
		{
			return (array_key_exists($key, $this->data))
				? $this->data[$key]
				: false;
		}
		return $this->data;
	}
	
	/**
	 *
	 */
	public function exists()
	{
		$result = $this->get();
		return !empty($result);
	}
	
	/**
	 *
	 */
	protected function create(array $data)
	{
		return $this->DataBase->createUser($this->id, $data);
	}
	
	/**
	 *
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 *
	 */
	public function set(array $data)
	{
		return $this->DataBase->setUserData($this->id, $data);
	}
}