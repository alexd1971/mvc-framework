<?php

return array(
	/**
	 * Путь к загрузчику классов относительно коренвого каталога web-сервера
	 *
	 * Загрузчик представляет собой класс Loader, реализующий статический метод autoLoad($class),
	 * выполняющий автоматическую загрузку нужного класса.
	 * По умолчанию используется загрузчик core/Loader.php
	 */

	"loader" => "core/Loader.php",

	/**
	 * Пространство имен приложения
	 */

	"appNamespace" => "app",

	"include_path" => array(),
);