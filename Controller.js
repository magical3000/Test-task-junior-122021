var lastmessageget = true;

function SendMessage(ms)
{
	lastmessageget = false;
	
	var data = "command="+ms;
	
	var answermessage = "";

	var xhr = new XMLHttpRequest(); // Создаём объект xhr

	xhr.open("POST", "Model.php" , true); // Открываем асинхронное соединение

	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Отправляем кодировку
	
	if (ms==1)
	{
		data = data+"&firstname="+document.getElementById("firstname").value;
		data = data+"&lastname="+document.getElementById("lastname").value;
		data = data+"&mobile="+document.getElementById("mobile").value;
		data = data+"&comment="+document.getElementById("comment").value;
	}

	xhr.send(data); // Отправляем POST-запрос
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4)
		{
			if (xhr.status == 200) // Если запрос успешен, обрабатываем результат
			{
				answermessage = JSON.parse(xhr.responseText);
				
				var error = Number.parseInt(answermessage.Error);
			 
				switch (error)
				{
					case 0:
						if (ms==1)
						{
							document.getElementById("answer").innerHTML = 'Пользователь успешно добавлен!';
						}
						else
						{
							document.getElementById("answer").innerHTML = answermessage.Table;
						}
						break;
					case 1:
						document.getElementById("answer").innerHTML = 'Ошибка: команда не распознана! Пожалуйста, свяжитесь с администратором при возникновении данной ошибки!';
						break;
					case 2:
						if (ms==1)
						{
							document.getElementById("answer").innerHTML = 'Ошибка: некорректное имя! Имя должно содержать не менее 2 символов.';
						}
						else
						{
							document.getElementById("answer").innerHTML = 'Ошибка: не удалось получить список клиентов! Пожалуйста, свяжитесь с администратором при возникновении данной ошибки!';
						}
						break;
					case 3:
						document.getElementById("answer").innerHTML = 'Ошибка: некорректная фамилия! Фамилия должна содержать не менее 2 символов.';
						break;
					case 4:
						document.getElementById("answer").innerHTML = 'Ошибка: некорректный номер телефона! Номер телефона должен содержать не менее 10 символов и может включать в себя только цифры.';
						break;
					case 5:
						document.getElementById("answer").innerHTML = 'Ошибка: не удалось добавить клиента! Пожалуйста, свяжитесь с администратором при возникновении данной ошибки!';
						break;
					default:
						break;
				}
			}
			else
			{
				document.getElementById("answer").innerHTML = 'Ошибка: проверьте соединение или попробуйте позже!';
			}
		}
		else
		{
			document.getElementById("answer").innerHTML = 'Ошибка: проверьте соединение или попробуйте позже!';
		}
		lastmessageget = true;
	}
}

document.getElementById("getclients").addEventListener("click", () => SendMessage(2));
document.getElementById("addclient").addEventListener("click", () => SendMessage(1));