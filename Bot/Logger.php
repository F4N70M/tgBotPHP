<?php

namespace Bot;

use Exception;

/**
 * 
 */
class Logger
{
	protected $file;
	
	public function __construct(string $logFile)
	{
		$this->file = $logFile;
		
		file_put_contents($this->file, "\n\n----------------\n\n".print_r(date("Y.m.d H:i"), true)."\n\n----------------\n\n", FILE_APPEND);

		// file_put_contents($this->file, "_SERVER ".print_r($_SERVER, true)."\n", FILE_APPEND);
		// file_put_contents($this->file, "_POST ".print_r($_POST, true)."\n", FILE_APPEND);
		// file_put_contents($this->file, "_GET ".print_r($_GET, true)."\n", FILE_APPEND);
	}

	public function push($data)
	{
		// $debug_backtrace = debug_backtrace();
		// $backtrace = "{$debug_backtrace[1]["class"]}::{$debug_backtrace[1]["function"]} : {$debug_backtrace[1]["line"]}";
		// file_put_contents($this->file, "\n".print_r($backtrace, true)."\n", FILE_APPEND);
		// foreach (func_get_args() as $value)
		// {
		// 	file_put_contents($this->file, print_r($value, true)."\n", FILE_APPEND);
		// }
		file_put_contents($this->file, print_r($data, true)."\n", FILE_APPEND);
	}
}