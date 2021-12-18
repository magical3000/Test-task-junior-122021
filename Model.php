<?php
require_once( "Classes/ClientsHandler.php" );

$MySQLcredentials = ['servername','username', 'password', 'database']; // Реквизиты для подключения к базе данных
$arr = array();

function testmobile($number)
{
	for($i=0; $i<mb_strlen($number); $i++) {
		if (is_numeric($number[$i])==false)
		{
			return false;
		}
	}
	return true;
}

if (!empty($_POST['command'])) // Проверяем, что переданна команда ...
{
	$command = (int)$_POST['command'];
	
	switch ($command) // ... и что она известна
	{
		case 1: // Команда 1 - добавить пользователя. Ошибки: 2 - Имя менее 2 символов, 3 - фамилия менее 2 символов, 4 - мобильный менее 10 символов или состоит не только из цифр, 5 - ошибка сервера.
			if (!empty($_POST['firstname']))
			{
				if (mb_strlen($_POST['firstname'])>=2)
				{
					if (!empty($_POST['lastname']))
					{
						if (mb_strlen($_POST['lastname'])>=2)
						{
							if (!empty($_POST['mobile']))
							{
								if (mb_strlen($_POST['mobile'])>=10 && testmobile($_POST['mobile']))
								{
									$conn = new SQLConnector($MySQLcredentials[0],$MySQLcredentials[1],$MySQLcredentials[2],$MySQLcredentials[3]); // Создаём коннектор
									$cl = new ClientsHandler($conn); // Передаём коннектор в обработчик
									if (!empty($_POST['comment']))
									{
										if ($cl->newclient($_POST['firstname'],$_POST['lastname'],$_POST['mobile'],$_POST['comment']))
										{
											$arr = array('Error' => '0',);
										}
										else
										{
											$arr = array('Error' => '5',);
										}
									}
									else
									{
										if ($cl->newclient($_POST['firstname'],$_POST['lastname'],$_POST['mobile'],null))
										{
											$arr = array('Error' => '0',);
										}
										else
										{
											$arr = array('Error' => '5',);
										}
									}
									unset($cl); //Закрываем обработчик и соединение
									unset($conn);
								}
								else
								{
									$arr = array('Error' => '4',);
								}
							}
							else
							{
								$arr = array('Error' => '4',);
							}
						}
						else
						{
							$arr = array('Error' => '3',);
						}
					}
					else
					{
						$arr = array('Error' => '3',);
					}
				}
				else
				{
					$arr = array('Error' => '2',);
				}
			}
			else
			{
				$arr = array('Error' => '2',);
			}
			break;
		case 2: // Команда 2 - вывести пользователей
			$conn = new SQLConnector($MySQLcredentials[0],$MySQLcredentials[1],$MySQLcredentials[2],$MySQLcredentials[3]);
			$cl = new ClientsHandler($conn);
			$result = $cl->getclients();
			
			if ($result!=null)
			{
				$arr = array('Error' => '0', 'Table' => $result,);
			}
			else
			{
				$arr = array('Error' => '2',);
			}
			
			unset($cl);
			unset($conn);
			
			break;
		default:
			$arr = array('Error' => '1',);
			break;
	}
}
else
{
	$arr = array(
	  'Error' => '1',
	);
}

print(json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
?>