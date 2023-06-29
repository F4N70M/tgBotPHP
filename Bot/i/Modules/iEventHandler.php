<?php

namespace Bot\i\Modules;

use Bot\i\iApi;
use Bot\i\Objects\iDataBase;

interface iEventHandler
{
	public function __construct(iApi $Api, iDataBase $db, array $config);
		// init config
		// get update

	public function getUpdate();
}