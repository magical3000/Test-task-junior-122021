<?php
require_once( "SQLConnector.php" );

class ClientsHandler
{
	protected SQLConnector $connector;
	
	public function __construct(SQLConnector $connector) // Указываем коннектор, через который будут отправляться sql запросы
	{
		$this->connector = $connector;
	}
	
	public function createtable() // Создаём таблицу Clients. Возвращает true в случае успеха, иначе false.
	{
		if ($this->connector->testconnect())
		{
			$sql = 'SHOW TABLES LIKE "Clients"';
			$query = $this->connector->sendquery($sql);
			if ($query != null)
			{
				if (mysqli_num_rows($query)==0)
				{
					$sql = "CREATE TABLE Clients (
						id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
						firstname VARCHAR(150) NOT NULL,
						lastname VARCHAR(150) NOT NULL,
						mobile TEXT NOT NULL,
						comment TEXT,
						reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
						)";
					
					$query = $this->connector->sendquery($sql);					
					if ($query != null)
					{
						return true;
					}
					else
					{
						$log = date('Y-m-d H:i:s') . ' Can\'t create table: SQL query error!';
						file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
					}
				}
				else
				{
					$log = date('Y-m-d H:i:s') . ' Can\'t create table: This table already exists!';
					file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
				}
			}
			else
			{
				$log = date('Y-m-d H:i:s') . ' Can\'t create table: SQL query error!';
				file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
			}
		}
		else
		{
			$log = date('Y-m-d H:i:s') . ' Can\'t create table: MySQL don\'t connect!';
			file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
		}
		
		return false;
	}
	
	public function newclient($firstname, $lastname, $mobile, $comment) // Добавляем нового клиента в таблицу
	{
		if ($this->connector->testconnect())
		{
			if ($comment==null)
			{
				$sql = 'INSERT INTO Clients (firstname, lastname, mobile) VALUES ("'.$firstname.'", "'.$lastname.'", "'.$mobile.'")';
			}
			else
			{
				$sql = 'INSERT INTO Clients (firstname, lastname, mobile, comment) VALUES ("'.$firstname.'", "'.$lastname.'", "'.$mobile.'", "'.$comment.'")';
			}
			
			$query = $this->connector->sendquery($sql);
			if ($query != null)
			{
				return true;
			}
			else
			{
				$log = date('Y-m-d H:i:s') . ' Can\'t add client: SQL query error!';
				file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
			}
		}
		else
		{
			$log = date('Y-m-d H:i:s') . ' Can\'t add client: MySQL don\'t connect!';
			file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
		}
		
		return false;
	}
	
	public function getclients() // Получаем список клиентов в виде html таблицы <table>
	{
		if ($this->connector->testconnect())
		{
			$sql = 'SHOW TABLES LIKE "Clients"'; // Проверяем наличие таблицы
			
			$query = $this->connector->sendquery($sql);
			if ($query != null)
			{
				if (mysqli_num_rows($query)!=0)
				{
					$sql = 'SHOW COLUMNS FROM Clients'; // Получаем поля таблицы Clients устанавливая их заголовками html таблицы
					
					$query = $this->connector->sendquery($sql);
					if ($query != null)
					{
						$result = "<table class=\"table table-inverse\"><tr>";
						$arr = array();
						while ($res = mysqli_fetch_array($query))
						{
							$result = $result."<th>".$res['Field']."</th>";
							$arr[count($arr)]=$res['Field'];
						}
						$result = $result."</tr>";
						
						$sql = 'SELECT * FROM Clients'; // Получаем содержимое таблицы Clients и заполняем html таблицу
						
						$query = $this->connector->sendquery($sql);
						if ($query != null)
						{
							while ($res = mysqli_fetch_array($query))
							{
								$result = $result."<tr>";
								for ($i=0; $i<count($arr); $i++)
								{
									$result = $result."<td>".$res[$arr[$i]]."</td>";
								}
								$result = $result."</tr>";
							}
							
							$result = $result."</table>";
							
							return $result;
						}
						else
						{
							$log = date('Y-m-d H:i:s') . ' Can\'t add client: SQL query error!';
							file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
						}
					}
					else
					{
						$log = date('Y-m-d H:i:s') . ' Can\'t add client: SQL query error!';
						file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
					}
				}
				else
				{
					$log = date('Y-m-d H:i:s') . ' Can\'t add client: Not found table!';
					file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
				}
			}
			else
			{
				$log = date('Y-m-d H:i:s') . ' Can\'t add client: SQL query error!';
				file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
			}
		}
		else
		{
			$log = date('Y-m-d H:i:s') . ' Can\'t get clients: MySQL don\'t connect!';
			file_put_contents(__DIR__ . '/log('.date('Y-m-d').').txt', $log . PHP_EOL, FILE_APPEND);
		}
		
		return null;
	}
}
?>