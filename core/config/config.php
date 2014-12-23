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
		 * Конфигурация дополнений, включенных в стандартную поставку фреймворка, которые могут быть использованы в приложении.
		 *
		 * Для загрузки сконфигурированного дополнения необходимо вызвать функцию: MVCF::app()->loadAssets(array("assetName",...));
		 *
		 * Общий вид конфигурации:
		 *
		 * array(
		 *
		 * 		"js" => array (
		 * 			"jquery" => array (
		 * 				"path" => "core/assets/js/jquery.min.js"
		 * 			),
		 *
		 * 			"bootstrap" => array (
		 * 				"path" => "core/assets/js/bootstrap.min.js",
		 * 				"depends" => array ("js.jquery", "css.bootstrap"),
		 * 			),
		 *
		 * 			...
		 *
		 * 		),
		 *
		 * 		"css" => array (
		 * 			"bootstrap" => array (
		 * 				"path" => "core/assets/css/bootstrap.min.css"
		 * 			),
		 *
		 * 			...
		 *
		 * 		)
		 * )
		 */

		"assets" => array (
		),
);