<?php


namespace Bot\Objects;


use Exception;
use InvalidArgumentException;
use PDO;
use PDOException;
use PDOStatement;

class DataBaseQueryBuilder
{


	/** EXAMPLES **

	* SELECT *
	-> select(array|null)
	-> from(string $table)
	-> where(array $where)
	-> orderBy()
	-> limit(int, int|null)
	-> result()

	* INSERT *
	-> insert()
	-> into(string $table)
	-> values(array $values)
	-> result()

	 * UPDATE *
	-> update(string $table)
	-> set(array $values)
	-> where(array)
	-> result()

	 * DELETE *
	-> delete()
	-> from(string $table)
	-> where(array $where)
	-> result()

	* SHOW *
	-> show(string TABLES|COLUMNS|INDEX|VARIABLES, string|null $table)
	-> result

	 */


    protected $connection;
	protected $_link;

	protected $sql;
	protected $query = [];
	protected $bind = [];
	protected $statement;


	public function __construct(PDO $link)
	{
	    // $this->connection = $connection;
		// $this->_link = $connection->get();
		$this->_link = $link;
	}


	/**
	 * @param string $sql
	 * @param array $params
	 * @return $this
	 */
	public function query(string $sql, array $params = [])
	{
		$db = clone $this;
		$db->sql = $sql;
		$db->bind = $params;

		return $db;
	}


	/**
	 * @param null $columns
	 * @return $this
	 * @throws Exception
	 */
	public function select($columns=null)
	{
		$db = clone $this;
		$db->bind = [];
		$db->sql = '';
		$db->query = [
			'action'    => 'SELECT',
			'columns'   => $this->columns($columns),
			'from'      => null,
			'join'      => [],
			'where'     => null,
			'orderBy'   => null,
			'limit'     => null
		];

		return $db;
	}


	/**
	 * @return $this
	 */
	public function insert()
	{
		$db = clone $this;
		$db->bind = [];
		$db->sql = '';
		$db->query = [
			'action'    => 'INSERT',
			'into'      => null,
			'values'    => null
		];

		return $db;
	}


	/**
	 * @param string $table
	 * @return $this
	 */
	public function update(string $table)
	{
		$db = clone $this;
		$db->bind = [];
		$db->sql = '';
		$db->query = [
			'action'    => 'UPDATE',
			'table'     => $table,
			'set'       => null,
			'where'     => null
		];

		return $db;
	}


	/**
	 * @return $this
	 */
	public function delete()
	{
		$db = clone $this;
		$db->bind = [];
		$db->sql = '';
		$db->query = [
			'action'    => 'DELETE',
			'from'      => null,
			'where'     => null
		];

		return $db;
	}


	/**
	 * SHOW TABLES [FROM db_name]; -  список таблиц в базе
	 * SHOW COLUMNS FROM tbl_name [FROM db_name]; - список столбцов в таблице
	 * SHOW INDEX FROM tbl_name; - список индексов
	 * SHOW VARIABLES - значения системных переменных
	 *
	 * SHOW DATABASES; - список баз данных
	 * SHOW CREATE TABLE table_name; - показать структуру таблицы в формате "CREATE TABLE"
	 * SHOW GRANTS FOR user [FROM db_name]; - привилегии для пользователя.
	 * SHOW [FULL] PROCESSLIST; - статистика по mysqld процессам
	 * SHOW STATUS; - общая статистика
	 * SHOW TABLE STATUS [FROM db_name]; - статистика по всем таблицам в базе
	 */
	/**
	 * @param $type
	 * @return QueryBuilder
	 */
	public function show($type, $from = null)
	{
		$showType = strtoupper($type);
		if (!(in_array($showType,['TABLES','VARIABLES']) OR (in_array($showType,['COLUMNS','INDEX']) && $from !== null)))
			throw new InvalidArgumentException("Invalid argument \"$type\" must be TABLES|COLUMNS|INDEX|VARIABLES");


		$db = clone $this;
		$db->bind = [];
		$db->sql = '';
		$db->query = [
			'action'    => 'SHOW',
			'show'      => $showType,
			'from'      => $from
		];

		return $db;
	}


	/**
	 * @param string $table
	 * @return $this
	 * @throws Exception
	 */
	public function from(string $table)
	{
		if (!array_key_exists('from',$this->query))
			throw new Exception("method \"from\" cannot be used with \"{$this->query['action']}\"");

		$this->query['from'] = $table;

		return $this;
	}


	/**
	 * @param string $table
	 * @return $this
	 * @throws Exception
	 */
	public function into(string $table)
	{
		if (!array_key_exists('into',$this->query))
			throw new Exception("method \"into\" cannot be used with \"{$this->query['action']}\"");

		$this->query['into'] = $table;

		return $this;
	}


	/**
	 * @param null $columns
	 * @return string|null
	 * @throws Exception
	 */
	protected function columns($columns=null)
	{
		if (empty($columns)) {
			$result = "*";
		}
		elseif (is_array($columns))
		{
			$result = implode(', ', $columns);
		}
		elseif (is_string($columns))
		{
			//TODO: Написать проверку по шаблону
			$result = $columns;
		}
		else
		{
			throw new Exception('Request Error: Invalid query result column format');
		}

		return $result;
	}


	/**
	 * @param array $values
	 * @return $this
	 * @throws Exception
	 */
	public function set(array $values=[])
	{
		if (!array_key_exists('set',$this->query))
			throw new Exception("method \"set\" cannot be used with \"{$this->query['action']}\"");

		if (!empty($values)) {
			$bindValues = [];
			foreach ($values as $key => $value)
			{
				$bindValues[$key] = ":$key";
				$this->bind[":{$key}"] = $value;
			}
			$this->query['set'] = $bindValues;
		}
		else
		{
			throw new Exception('Request failed: no values to update');
		}

		return $this;
	}

	/**
	 * @param array $values
	 * @return $this
	 * @throws Exception
	 */
	public function values(array $values=[])
	{
		if (!array_key_exists('values',$this->query))
			throw new Exception("method \"values\" cannot be used with \"{$this->query['action']}\"");

		if (isset($this->query["into"]) && !empty($this->query["into"]))
		{
			$tmp = new DataBaseQueryBuilder($this->_link);
			$currentColumns = array_keys($tmp
				->show("COLUMNS", $this->query["into"])
				->resultBy("Field"));
		}

		if (!empty($values))
		{
			$bindValues = [];
			foreach ($values as $key => $value)
			{
				if (!in_array($key, $currentColumns)) continue;

				$bindValues[$key] = ":$key";
				$this->bind[":$key"] = $value;
			}

			$result = $bindValues;

			$this->query['values'] = $result;
		}
		else
		{
			throw new Exception('Request failed: no values to update');
		}

		return $this;
	}

	/**
	 * @param array|string $orderBy
	 * @return $this
	 * @throws Exception
	 *
	 * @example ['date']
	 * @result  date ASC
	 *
	 * @example ['date'=>true, name=>false]
	 * @result  date ASC, date DESC
	 */
	public function orderBy($orderBy)
	{
		if (!array_key_exists('orderBy',$this->query))
			throw new Exception("method \"orderBy\" cannot be used with \"{$this->query['action']}\"");

		if (!empty($orderBy))
		{
			if ( is_array($orderBy) )
			{
				$result = [];
				foreach ($orderBy as $key => $value)
				{
					if (is_numeric($key))
					{
//						$this->sql .= " $value ASC";
						$result[] = "$value ASC";
					}
					else
					{
//						$this->sql .= " $key " . ((!$value || strtoupper($value) == "DESC") ? "DESC" : "ASC");
						$result[] = "$key " . ((!$value || strtoupper($value) == "DESC") ? "DESC" : "ASC");
					}
				}
				$result = implode(', ', $result);
			}
			/*//TODO: Написать проверку с ASC и DESC
			elseif(is_string($orderBy) && preg_match('/^[A-z0-9\-_, ]+$/', $orderBy))
			{
				$result = $orderBy;
			}*/
			else
			{
				throw new Exception('Invalid argument orderBy');
			}
			$this->query['orderBy'] = $result;
		}

		return $this;
	}

	/**
	 * @param int|null $count
	 * @param int|null $offset
	 * @return $this
	 * @throws Exception
	 */
	public function limit(int $count=null, int $offset=null)
	{
		if (!array_key_exists('limit',$this->query))
			throw new Exception("method \"limit\" cannot be used with \"{$this->query['action']}\"");

		if ($count !== null)
			$this->query['limit'] = $count . ($offset !== null ? " OFFSET {$offset}" : "");
		return $this;
	}


	/**
	 * @param string $column
	 * @return array
	 * @throws Exception
	 */
	public function resultBy(string $column)
	{
		$resultBy = [];

		$result = $this->result();

		foreach ($result as $item)
		{
			if (isset($item[$column]))
			{
				$resultBy[$item[$column]] = $item;
			}
			else
			{
				break;
			}
		}
		return $resultBy;
	}


	/**
	 * @return array
	 * @throws Exception
	 */
	public function result($columnKey = false)
	{
		$this->sql = $this->getConvertQuery($this->query);
		$statement = $this->prepare($this->sql, $this->bind);
		$statement = $this->execute($statement);
		$result = $this->getStatementResult($this->query['action'], $statement, $columnKey);

		return $result;
	}


    /**
     * @return array
     * @throws Exception
     */
    public function debug()
    {
        $result = $this->result();
        echo "<pre>";
		var_dump($this->sql, $this->bind);
		echo "</pre>";
        return $result;
    }

	/**
	 * @param $parts
	 * @return string
	 */
	protected function getConvertQuery($parts)
	{
		$query = [];
		// echo "<pre>";
		// print_r($parts);
		// echo "</pre>";
		foreach ($parts as $key => $value)
		{
			if (empty($value)) continue;
			if ($key == 'into') $value = "INTO {$value}";
			if ($key == 'from') $value = "FROM {$value}";
			if ($key == 'where') $value = "WHERE {$value}";
			if ($key == 'set')
			{
//                $value = $this->intersectTableData($parts['table'],$value);
				array_walk($value, function(&$item, $key) {
					$item = "$key=$item";
				});
				$value = 'SET '.implode(',', $value);
			}
			if ($key == 'values')
			{
//                $value = $this->intersectTableData($parts['into'],$value);
			    $value = "(".implode(", ", array_keys($value)).") VALUES (".implode(", ", $value).")";
			}
			if ($key == 'orderBy') $value = "ORDER BY {$value}";
            if ($key == 'limit') $value = "LIMIT {$value}";
            if ($key == 'show') $value = "{$value}";

			$query[] = $value;
		}
		$result = implode(' ', $query);
		return $result;
	}


    public function intersectTableData(string $table, array $data)
    {
        $tmp = clone $this;
        $columnsRequest = $tmp->show('COLUMNS', $table)->result();
        $columns = [];
        foreach ($columnsRequest as $item)
        {
            $columns[$item['Field']] = null;
        }
        $data = array_intersect_key($data, $columns);
        foreach ($data as $key => $value)
        {
            if (empty($value)) $data[$key] = null;
        }
        return $data;
    }
    public function getUniqueName(string $table, string $name)
    {
        $tmp = clone $this;

        $i = 0;
        do
        {
            $resultName = $name . ($i > 0 ? '-'.$i : null);
            $issetName = $tmp
                ->select()
                ->from($table)
                ->where(['name'=>$resultName])
                ->result();
            $i++;
        }
        while (!empty($issetName));
        return $resultName;
    }

	/**
	 * @param string $queryAction
	 * @param PDOStatement|false $statement
	 * @return array|bool|string
	 */
	protected function getStatementResult(string $queryAction, $statement, $columnKey=false)
	{
		if($statement)
		{
			if ($queryAction == 'INSERT')
			{
				$result = $this->_link->lastInsertId();
			}
			elseif ($queryAction == 'SELECT')
			{
			    if ($columnKey)
                {
                	$result = [];
                    while ($row = $statement->fetch(PDO::FETCH_ASSOC))
                    {
                        $result[$row[$columnKey]] = $row;
                    }
                }
                else
                {
				    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                }
			}
			elseif ($queryAction == 'UPDATE' || $queryAction == 'DELETE')
			{
				$result = $statement->rowCount();
			}
			elseif ($queryAction == 'SHOW')
			{
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			}
			else
			{
				$result = true;
			}
		}
		else
		{
			$result = false;
		}

		return $result;
	}











	/**
	 * @param $sql
	 * @param array $bind
	 * @return bool|PDOStatement
	 */
	protected function prepare($sql, array $bind)
	{
		try
		{
			$statement = $this->_link->prepare($sql);
			if (!empty($bind))
			{
				foreach ($bind as $key => $value)
				{
					// False to NULL
					if ($value === false) $value = null;

					// if (is_array($value) && is_object($value))
					// {
					// 	debug($key, $value);
					// }
					/*if (is_json($value))
                    {
                        $v = str_replace('\"', '\\\\"', $value);
                        debug($v);
                    }*/
					$statement->bindValue($key, $value);
				}
			}
		}
		catch (PDOException $e)
		{
			echo "Request failed: ";
			echo $e->getMessage();
			$statement = null;
		}

		return $statement;
	}

	/**
	 * @param PDOStatement $statement
	 * @return PDOStatement
	 * @throws Exception
	 */
	protected function execute(PDOStatement $statement)
	{
		try
		{
			$result = $statement->execute();
			if (!$result)
				$statement = false;
		}
		catch (PDOException $e)
		{
			echo "Request error: ";
			echo $e->getMessage();

			$statement = false;
		}
		return $statement;
	}


	/**
	 * @param null $where
	 * @return $this
	 */
	public function where($where=null)
	{
		$whereResult = $this->recursiveWhere($where);
		if ( !empty($whereResult['where']) )
		{
			$this->query['where'] = $whereResult['where'];
			$this->bind = array_merge($this->bind, $whereResult['bind']);
		}

		return $this;
	}

	/**
	 * @param   $where
	 * @return  array
	 *
	 * @example ['id'=>5]
	 * @result  WHERE id = 5
	 *
	 * @example ['id'=>['>=', 7]]
	 * @result  WHERE id >= 7
	 *
	 * @example ['id'=>['<', 4], ['or', 'id'=>['>', 6]]]
	 * @result  WHERE id < 4 || id > 6
	 *
	 * @example ['id'=>['like', '%_fw%']]
	 * @result  WHERE id LIKE '%_fw%'
	 *
	 * @example ['date'=>['between', ['2019-03-03', '2019-03-05']]]
	 * @result  WHERE date BETWEEN '2019-03-03' AND '2019-03-05'
	 *
	 * @example ['date'=>['in', ['2019-03-04', '2019-03-11']]]
	 * @result  WHERE date IN ('2019-03-04', '2019-03-11')
	 */
	protected function recursiveWhere($where, $bind = [])
	{
//		$result = '';

		$result = [
			'where' => '',
			'bind'  => $bind
		];

//		$result['where'] = '';
//		$bind = [];

		if (!empty($where))
		{

			if (is_array($where))
			{
				$result['where'] = '';
				foreach ($where as $key => $item) {
					if (is_numeric($key) && is_array($item))
					{
						if ($this->isset_glue($item))
						{
							$glue = $this->get_glue($item);
							foreach ($item as $ikey => $value)
							{
								if ( is_numeric($ikey) )
								{
									$tmp = $this->recursiveWhere($value, $result['bind']);
                                    if (!empty($tmp['where']))
                                        $result['where'] .= " $glue (".$tmp['where'].")";
                                    if (!empty($tmp['bind']))
                                    {
                                        foreach ($tmp['bind'] as $tmpKey => $tmpVal)
                                        {
                                            $result['bind'][$tmpKey] = $tmpVal;
                                        }
                                    }
								}
								else
								{
									if (!is_array($value))
										$value = ['=',$value];
									$tmp = $this->parseValueWhere($value, count($result['bind']));
									foreach ($tmp['bind'] as $tmpKey => $tmpVal)
									{
										$result['bind'][$tmpKey] = $tmpVal;
									}
									if (!empty($tmp['where']))
										$result['where'] .= " $glue $ikey ".$tmp['where'];
								}
							}
						}
						else
						{
							$tmp = $this->recursiveWhere($item, $result['bind']);

							if (!empty($tmp['where']))
							{
								if ( !empty($result['where']) ) $result['where'] .= " && ";
								$result['where'] .= '(' . $tmp['where'] . ')';
							}
                            if (!empty($tmp['bind']))
                            {
                                foreach ($tmp['bind'] as $tmpKey => $tmpVal)
                                {
                                    $result['bind'][$tmpKey] = $tmpVal;
                                }
                            }
						}

					}
					elseif(is_string($key) && !empty($key))
					{
						if(is_scalar($item))
						{
							if ( !empty($result['where']) ) $result['where'] .= " && ";
							$bindName = ":where" . count($result['bind']);
							$result['bind'][$bindName] = $item;
							$result['where'] .= "$key = $bindName";
						}
						elseif (is_null($item))
						{
							if ( !empty($result['where']) ) $result['where'] .= " && ";
							$result['where'] .= "$key IS NULL";
						}
						elseif (count($item) == 2 && isset($item[0]) && is_string($item[0]))
						{
							$tmp = $this->parseValueWhere($item, count($result['bind']));
							foreach ($tmp['bind'] as $tmpKey => $tmpVal)
							{
								$result['bind'][$tmpKey] = $tmpVal;
							}
							if (!empty($tmp['where']))
							{
								if ( !empty($result['where']) ) $result['where'] .= " && ";
								$result['where'] .= "$key ".$tmp['where'];
							}
						}
					}
					elseif (is_numeric($key) && is_string($item) && !empty($item))
					{
						if ( !empty($result['where']) ) $result['where'] .= " && ";
						$result['where'] .= $item;
					}
				}
			}
			elseif(
				is_string($where)
				&&
				!in_array(strtoupper($where),['AND','OR','&&','||'])
			)
			{
				$result['where'] = $where;
			}
			else
			{
				#
			}
		}

		return $result;
	}

	/**
	 * @param array $item
	 * @return bool
	 */
	protected function isset_glue(array $item)
	{
		return (count($item) == 2 && isset($item[0]) && is_string($item[0]) && in_array(strtoupper($item[0]), ['AND','OR','&&','||']));
	}

	/**
	 * @param array $item
	 * @return string
	 */
	protected function get_glue(array $item)
	{
		return (in_array(strtoupper(array_shift($item)), ['OR','||']) ? '||' : '&&');
	}

	/**
	 * @param $array
	 * @param $bindCount
	 * @return array|string
	 */
	protected function parseValueWhere($array, $bindCount = 0)
	{
		$where = '';
		$bind  = [];
		$operator = strtoupper(array_shift($array));
		foreach ($array as $value)
		{
			if (in_array($operator, ['=','!=','>','<','>=','<=','<>','LIKE']) && is_scalar($value))
			{
			    if ($operator == '!=') $operator = '<>';
				$bindName = ":where" . $bindCount; $bindCount++;
				$bind[$bindName] = $value;
				$where .= "$operator $bindName";
			}
			elseif ($operator == 'BETWEEN' && is_array($value) && count($value) == 2 && is_scalar($value[0]) && !empty($value[0]) && is_scalar($value[1]) && !empty($value[1]))
			{
				$where = "$operator ";

				$bindName = ":where" . $bindCount; $bindCount++;
				$bind[$bindName] = $value[0];
				$where .= $bindName;

				$where .= " AND ";

				$bindName = ":where" . $bindCount; $bindCount++;
				$bind[$bindName] = $value[1];
				$where .= $bindName;
			}
			elseif ($operator == 'IN' && is_array($value) && !empty($value) )
			{
				$in = [];
				foreach ($value as $inValue)
				{
					if ( is_scalar($inValue) || is_null($inValue) )
					{
						$in[] = $inValue;
					}
					else
					{
						$in = [];
						break;
					}
				}

				if (!empty($in))
				{
					foreach ($in as $key => $item)
					{
						$bindName = ":where" . $bindCount; $bindCount++;
						$bind[$bindName] = $item;
						$in[$key] = $bindName;
					}
					$where = "$operator (" . implode(',', $in) . ")";
				}
			}
			break;
		}

		return ['where'=>$where,'bind'=>$bind];
	}
}