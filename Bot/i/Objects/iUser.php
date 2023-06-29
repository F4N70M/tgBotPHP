<?php

namespace Bot\Objects;

/**
 * 
 */
interface iUser
{
	public function __construct(int|string $id);

	public function getData();
}