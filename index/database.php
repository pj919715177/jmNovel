<?php
class database
{
	private $dsn;
	private $user;
	private $password;
	private $pdo;
	public function __construct()
	{
		$this->dsn = 'mysql:dbname=farmoe_jm_novel;host=127.0.0.1';
		$this->user = '';
		$this->password = '';
		try {
			$this->pdo = new PDO($this->dsn, $this->user, $this->password);
			$this->pdo->query('set names utf8');
		} catch (Exception $e) {
			echo '服务器错误！';die;
		}
	}

	public function execute($sql, $param = [])
	{
		$statement = $this->pdo->prepare($sql);
		$result = $statement->execute($param);
		return $statement;
	}

	//多行插入
	// public function multipleInsert($table, $data)
	// {
	// 	if (!isset($data[0]) || !is_array($data[0]) || !$data[0]) {
	// 		return false;
	// 	}

	// 	$result = 0;
	// 	$column = array_keys($data[0]);
	// 	$commonSql = "INSERT INTO {$table}(";
	// 	foreach ($column as $value) {
	// 		$commonSql .= '`' . $value . '`,';
	// 	}
	// 	$commonSql = rtrim($commonSql, ',');
	// 	$commonSql .= ') ';

	// 	$count = count($data);
	// 	$length = 25;
	// 	for ($index = 0; $index < $count; $index += 25)
	// 	{
	// 		$index + $length > $count && $length = $count - $index;
	// 		$tempData = array_slice($data, $index, $length);
	// 		if ($tempData) {
	// 			$param = [];
	// 			$sql = $commonSql . 'VALUES';
	// 			$order = 0;
	// 			foreach ($tempData as $key => $item) {
	// 				$temp = '(:' . implode("{$order},:", $column);
	// 				$temp .= "{$order}),";
	// 				foreach ($column as $value) {
	// 					$param[":{$value}{$order}"] = $item[$value];
	// 				}
	// 				$sql .= $temp;
	// 				$order++;
	// 			}
	// 			$sql = rtrim($sql, ',');
	// 			$result += (int)$this->execute($sql, $param)->rowCount();
	// 		}
	// 	}
	// 	return $result;
	// }

	//插入一条数据
	// public function insertData($table, $data)
	// {
	// 	if (!is_array($data) || !$data) {
	// 		return false;
	// 	}

	// 	$sql = "INSERT INTO {$table}(";
	// 	$value = "VALUES(";
	// 	$param = [];
	// 	foreach ($data as $key => $item) {
	// 		$sql .= "{$key},";
	// 		$value .= ":{$key},";
	// 		$param[":{$key}"] = $item;
	// 	}
	// 	$sql = rtrim($sql, ',');
	// 	$sql .= ')';
	// 	$sql .= rtrim($value, ',');
	// 	$sql .= ');';
	// 	return $this->execute($sql, $param)->rowCount();
	// }

	//更新数据
	// public function updateData($table, $data, $where, $param)
	// {
	// 	$sql = "UPDATE {$table} SET ";
	// 	foreach ($data as $key => $value) {
	// 		$sql .= "{$key}=:{$key},";
	// 		$param[":{$key}"] = $value;
	// 	}
	// 	$sql = rtrim($sql, ',');
	// 	$sql .= " WHERE {$where}";
	// 	return $this->execute($sql, $param)->rowCount();
	// }

	//返回最后一行的信息
	// public function getLastId()
	// {
	// 	return $this->pdo->lastInsertId();
	// }

	//通过id更新数据
	// public function updateDataById($table, $data, $id)
	// {
	// 	$where = "id=:id";
	// 	$param = [':id' => $id];
	// 	return $this->updateData($table, $data, $where, $param);
	// }


	//查找数据列表
	public function getDataList($table, $select = '*', $where = 1, $param = [], $order = 'id desc', $limit = 20) 
	{
		if(!is_string($select)) {
			return [];
		}
		$sql = "SELECT {$select} FROM {$table} WHERE {$where} ORDER BY {$order}";
		if ($limit) {
			$sql .= " LIMIT {$limit}";
		}
		return $this->execute($sql, $param)->fetchAll(PDO::FETCH_ASSOC);
	}

	//获取一行数据
	public function getDataDetail($table, $select = '*', $where = 1, $param = [], $order = 'id desc') 
	{
		if(!is_string($select)) {
			return [];
		}
		$sql = "SELECT {$select} FROM {$table} WHERE {$where} ORDER BY {$order} LIMIT 1";
		return $this->execute($sql, $param)->fetch(PDO::FETCH_ASSOC);
	}

	//获取表中最大id
	// public function getMaxId($table) 
	// {
	// 	$sql = "SELECT MAX(`id`) as maxId FROM {$table}";
	// 	$result = $this->execute($sql)->fetch(PDO::FETCH_ASSOC);
	// 	return $result['maxId'];
	// }
}