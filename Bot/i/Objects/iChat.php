<?php

namespace Bot\Objects;

/**
 * 
 */
interface iChat
{
	public function __construct(int|string $id);

	public function getData();
}