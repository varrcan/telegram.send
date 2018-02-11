<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';
$APPLICATION->SetTitle('Инструкция по созданию бота и настройке модуля');
?>

<ol>
	<li>
		<p>Открываем Telegram и вбиваем в поиск <code>@BotFather</code>.</p>
	</li>

	<li>
		<p>Нажимаем кнопку ЗАПУСТИТЬ (START), она отправит в чат команду <code>/start</code></p>
	</li>

	<li>
		<p>Вводим в чат <code>/newbot</code>. Эта команда запустит процесс создания бота.</p>
	</li>

	<li>
		<p>В ответ на это BotFather запросит имя для бота.</p>
	</li>

	<li>
		<p>Затем уникальное имя бота – то, по которому вы сможете найти его в поиске. При этом уникальное имя
		   обязательно должно заканчиваться на <code>bot</code>. Например, HiiBot.</p>
	</li>

	<li>
		<p>Готово! Скопируйте полученный ключ API, найдите вашего бота через поиск и нажмите ЗАПУСТИТЬ (START)</p>
	</li>

	<li>
		<p>Дальше перейдите в <a href="<?='telegram_main.php?lang=' . LANGUAGE_ID?>">настройки</a> модуля и вставьте ключ в поле "Токен бота"</p>
	</li>

	<li>
		<p>Включите модуль, выберите сообщения, которые нужно отправлять в телеграм.</p>
	</li>

	<li>
		<p>На вкладке Пользователи запросите обновления, вы увидите ваш запрос.</p>
	</li>

	<li>
		<p>После добавление пользователя в систему, все выбранные сообщения будут пересылаться вам в телеграм.</p>
	</li>

</ol>

<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
