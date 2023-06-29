<?php

namespace Bot\i\Objects;

interface iDataBase
{
	public function __construct();

	public function createUser(int $id, array $data);

	public function userExists(int $id);

	public function getUserData(int $id);

	public function setUserData(int $id, array $data);
}