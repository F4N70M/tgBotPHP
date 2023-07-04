<?php


namespace Bot\Objects;


use PDO;
use Exception;

class DataBaseConnection
{
	private $_link;

	public $microtime = [];

	public function __construct($host, $user, $password, $base)
	{
		// $dsn = "mysql:host=".$host.";dbname=".$base.";charset=utf8";
		$dsn = "mysql:host=".$host.";dbname=".$base.";charset=utf8mb4";

		try
		{
			$this->_link = new PDO($dsn, $user, $password);
			// $this->_link->query('SET NAMES utf8');
			$this->_link->query('SET NAMES utf8mb4');
		}
		catch (Exception $e)
		{
			throw new Exception("failed to connect to database");
		}
	}




	/**
	 * @return PDO
	 */
	public function get()
	{
		return $this->check() ? $this->_link : false;
	}




	/**
	 * @return bool
	 */
	private function check()
	{
		return !empty($this->_link);
	}
}