<?php

namespace Bot\i\Objects;

interface iUpdate
{
	public function __construct(array $data);

	public function getData();
	// Message
	public function isMessage();
	public function getText();
	// From
	public function getFrom();
	public function getFromId();
}