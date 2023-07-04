<?php

namespace Bot\i\Objects;

use PDO;

interface iDataBase
{
	public function __construct(PDO $link);

	public function createUser(int $id, array $data);

	// public function userExists(int $id);

	public function getUserData(int $id);

	public function setUserData(int $id, array $data);
}