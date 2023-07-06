<?php

namespace Bot\i;

use Bot\i\iCore;
use Bot\i\iApi;
use Bot\i\Objects\iDataBase;

interface iUpdateHandler
{
	public function __construct(iCore $bot, array $config);
		// init config
		// get update

	public function getUpdate();
}