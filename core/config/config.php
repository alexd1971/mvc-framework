<?php
return array (
		/**
		 * Путь к загрузчику классов относительно корневого каталога web-сервера
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

		/**
		 * Дополнительные пути для поиска классов
		 */

		"include_path" => array (),

		/**
		 * Стандартная конфигурация метаданных фреймворка.
		 *
		 * Для загрузки сконфигурированного дополнения необходимо вызвать функцию: MVCF::app()->loadAssets(array("assetName",...));
		 *
		 * Общий вид конфигурации:
		 *
		 * array(
		 *
		 * 		"jquery" => array (
		 * 			"type" => "javascript",
		 * 			"url" => "core/assets/js/jquery.min.js"
		 * 		),
		 *
		 * 		"bootstrap-js" => array (
		 * 			"type" => "javascript",
		 * 			"url" => "core/assets/js/bootstrap.min.js",
		 * 			"depends" => array ("jquery", "bootstrap-css"),
		 * 		),
		 *
		 * 		"bootstrap-css" => array (
		 * 			"type" => "css",
		 * 			"url" => "core/assets/css/bootstrap.min.css"
		 * 		),
		 *
		 * )
		 */

		"assets" => array (
		),
);