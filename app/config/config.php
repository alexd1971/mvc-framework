<?php
return array (

		"dbConnections" => array (
				'db' => array (
						'driver' => 'pgsql',
						'host' => 'localhost',
						'port' => '5432',
						'dbname' => 'pjrkt',
						'user' => 'uideveloper',
						'password' => 'Qq1234567!!'
				)
		),

		/**
		 * Конфигурация дополнений, которые могут загружаться приложением.
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
		/**
		 * Путь к каталогу шаблонов
		 * Путь указывается относительно корневого каталога приложения
		 */
		"templates"		=> 'templates',

);
