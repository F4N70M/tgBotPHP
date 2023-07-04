<?php

namespace Bot\i\Objects;

use Bot\i\Objects\iDataBase;

/**
 * 
 */
interface iUser
{
	public function __construct(iDataBase $DataBase, int|array $id_data);

	public function get(string $key = null);

	public function exists();

	// public function create(array $data);

	public function getId();

	public function set(array $data);
}