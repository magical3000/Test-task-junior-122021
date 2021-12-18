<?php
class SQLConnector
{
	protected $connection;

    public function __construct($servername, $username, $password, $database, $port = null, $socket = null)
	{
		$this->openconnect($servername, $username, $password, $database, $port, $socket);
	}
	
	function __destruct() {
		$this->closeconnect();
	}
	
	public function openconnect($servername, $username, $password, $database, $port, $socket) // Подключаемся к базе данных
	{
		$this->connection = mysqli_connect($servername, $username, $password, $database, $port, $socket);
		if (!($this->testconnect()))
		{
			$log = date('Y-m-d H:i:s') . ' Can\'t connect to MySQL: '.mysqli_connect_error();
			file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
		}
	}
	
	public function testconnect() // Проверяем, подключены ли мы к базе данных
	{
		return ($this->connection==true);
	}
	
	public function sendquery($sql) // Отправляем SQL запрос и получаем ответ или null если не удалось отправить запрос
	{
		if ($this->testconnect())
		{
			if ($query = mysqli_query($this->connection,$sql))
			{
				return $query;
			}
			else
			{
				$log = date('Y-m-d H:i:s') . ' Can\'t send sql query! ErrorNum: '.mysqli_errno($this->connection) . " Error : " . mysqli_error($this->connection);
				file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
				return null;
			}
		}
		else
		{
			$log = date('Y-m-d H:i:s') . ' Can\'t send sql query: MySQL don\'t connect!';
			file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
			return null;
		}
	}
	
	public function closeconnect() // Закрываем соединение
	{
		if ($this->testconnect())
		{
			mysqli_close($this->connection);
		}
		else
		{
			$log = date('Y-m-d H:i:s') . ' Can\'t close connect: MySQL don\'t connect!';
			file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
		}
	}
}
?>