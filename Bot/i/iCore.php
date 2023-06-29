<?php

namespace Bot\i;

interface iCore
{

	public function __construct(string $token, array $config = []);

	// public function setFunction(string $name, callable $func);

	/**
	 * Получить обновление
	 */
	// public function getUpdate();

	/**
	 * Обработать обновление
	 */
	public function processUpdate();

}


/*


- Updater - класс, который используется для обновления сообщений и вызова соответствующих обработчиков.
- CommandHandler - класс, который отвечает за обработку команд бота.
- MessageHandler - класс, который отвечает за обработку сообщений, которые не являются командами.
- ChosenInlineResultHandler - класс, который отвечает за обработку выбранного inline-результата.
- CallbackQueryHandler - класс, который отвечает за обработку нажатий на кнопки в сообщениях.
- ConversationHandler - класс, который позволяет создавать диалоги с пользователями.



Функциональность бота

Direct
- Обрабатывать прямые запросы (Тет-а-тет)
	- Команды			// command
	- Цепочки скриптов	// script
Chats
- Обрабатывать запросы в чатах
	- Админ				// admin
	- Триггеры			// trigger
	- Система рейтинга	// rate
		- АвтоБан			// blacklist
- Выполнять задания
	- Программа			// programm




1. Запрос
2. Определить источник (Direct/Chat)
3. Определить исполняемую программу
4. Выполнить программу


Скрипт:
	- name
	- steps
		- step
			- 

command
	destination		// назначения: direct | chat





Команда > Запуск скрипта



Чат-триггер:
1. Запрос
2. Поиск слов в сообщении
3. Отправить ответ/отчет






Блоки:
1. Вывод сообщения (self|id|@username, text)
2. 
*/



