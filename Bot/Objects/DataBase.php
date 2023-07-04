<?php

namespace Bot\Objects;

use PDO;
use Bot\i\Objects\iDataBase;
use Bot\Objects\DataBaseQueryBuilder;

class DataBase implements iDataBase
{
	protected $link;
	protected $QueryBuilder;
	/**
	 *
	 */
	public function __construct(PDO $link)
	{
		$this->link = $link;
		$this->QueryBuilder = new DataBaseQueryBuilder($link);
	}

	public function createUser(int $id, array $data)
	{
		$result = $this->QueryBuilder
			-> insert()
			-> into("users")
			-> values($data)
			-> result();
		return $result;
	}

	public function getUser(int $id)
	{
		$result = $this->QueryBuilder
			-> select()
			-> from("users")
			-> where(["id"=>$id])
			-> result();
		return $result;
	}

	// public function userExists(int $id)
	// {
	// 	$result = $this->QueryBuilder
	// 		-> select()
	// 		-> from("users")
	// 		-> where(["id"=>$id])
	// 		-> result();
	// 	return !empty($result);
	// }

	public function getUserData(int $id)
	{

	}

	public function setUserData(int $id, array $values)
	{
		$result = $this->QueryBuilder
			-> update("users")
			-> set($values)
			-> where(["id"=>$id])
			-> result();
		return $result;
	}
}