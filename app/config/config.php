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
		 * Конфигурация метаданных, которые могут включаться приложением.
		 *
		 * Для загрузки сконфигурированного дополнения необходимо вызвать функцию: MVCF::app()->loadMeta(array("assetName",...));
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
		 * 				"type" => "javascript",
		 * 				"url" => "core/assets/js/bootstrap.min.js",
		 * 				"depends" => array ("jquery", "bootstrap-css"),
		 * 		),
		 *
		 * 		"bootstrap-css" => array (
		 * 			"type" => "css",
		 * 			"url" => "core/assets/css/bootstrap.min.css"
		 * 		),
		 * 
		 * 		"some_script" => array (
		 * 			"type" => "javascript",
		 * 			"text" => <<<SCRIPT
		 * 
		 * Text of script
		 * 
		 * SCRIPT,
		 * 		),
		 * 
		 * 		"some_css" => array (
		 * 			"type" => "css",
		 * 			"text" => <<<CSS
		 * 
		 * Text of css
		 * 
		 * CSS,
		 * 		),
		 * 
		 * 		"title" => array (
		 * 			"type" => "title",
		 * 			"title" => "Some Title" 
		 * 		)
		 * )
		 */

		"meta" => array (
		),
		
		/**
		 * Путь к каталогу шаблонов
		 * Путь указывается относительно корневого каталога приложения
		 */
		"templates"		=> 'templates',

);
