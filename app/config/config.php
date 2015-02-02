<?php
return array (
		/**
		 * Если true, то приложение автоматически выполняет запуск сессии
		 */
		"sessionAutoStart" => true,
		/**
		 * Конфигурация подключений к БД
		 */
		"dbConnections" => array (
				'db' => array (
						'driver' => 'pgsql',
						'host' => 'localhost',
						'port' => '5432',
						'dbname' => 'orkk_db',
						'user' => 'orkk_user',
						'password' => 'orkk_p@ssw0rd'
				)
		),
		/**
		 * Конфигурация модулей приложения
		 *
		 * 	"modules" => array (
		 *		"admin" => array (
		 *				"defaultController" => 'Dashboard'
		 *		)
		 *	),
		 *
		 */
		"modules" => array (),
		/**
		 * Контроллер по умолчанию
		 */
		"defaultController" => "Index",

		/**
		 * Конфигурация метаданных, которые могут включаться приложением.
		 *
		 * Для загрузки сконфигурированного дополнения необходимо вызвать функцию: MVCF::app()->loadAssets(array("assetName",...));
		 *
		 * Общий вид конфигурации:
		 *
		 * array(
		 *
		 * "jquery" => array (
		 * "type" => "javascript",
		 * "url" => "core/assets/js/jquery.min.js"
		 * ),
		 *
		 * "bootstrap-js" => array (
		 * "type" => "javascript",
		 * "url" => "core/assets/js/bootstrap.min.js",
		 * "depends" => array ("jquery", "bootstrap-css"),
		 * ),
		 *
		 * "bootstrap-css" => array (
		 * "type" => "css",
		 * "url" => "core/assets/css/bootstrap.min.css"
		 * ),
		 *
		 * "some_script" => array (
		 * "type" => "javascript",
		 * "text" => <<<SCRIPT
		 *
		 * Text of script
		 *
		 * SCRIPT,
		 * ),
		 *
		 * "some_css" => array (
		 * "type" => "css",
		 * "text" => <<<CSS
		 *
		 * Text of css
		 *
		 * CSS,
		 * )
		 * )
		 */

		"assets" => array (

				"jquery" => array (
						"type" => "javascript",
						"url" => "app/assets/js/jquery.min.js"
				),

				"bootstrap_js" => array (
						"type" => "javascript",
						"url" => "app/assets/js/bootstrap.min.js",
						"depends" => array (
								"jquery",
								"bootstrap_css"
						)
				),

				"bootstrap_css" => array (
						"type" => "css",
						"url" => "app/assets/css/bootstrap.min.css"
				),

				"font-awesome" => array (
						"type" => "css",
						"url" => "app/assets/css/font-awesome.min.css"
				),

				"style" => array (
						"type" => "css",
						"url" => "app/assets/css/style.css"
				),
		),

		/**
		 * Дополнения, которые должны включаться на каждой странице приложения
		 */

		"addAssets" => array (
				"bootstrap_js",
				"font-awesome"
		),

		/**
		 * Конфигурация meta-элементов заголовка
		 */

		"meta" => array (

				"meta" => array (
						"charset" => "utf-8",
						array (
								"name" => "keywords",
								"content" => ""
						),
						array (
								"name" => "description",
								"content" => ""
						),
						array (
								"name" => "viewport",
								"content" => "width=1024"
						)
				)
		),

		/**
		 * Конфигурация представления, используемое приложением
		 */

		"view" => array (

				// По умолчанию используется класс \core\View
				// "class" => '\core\View',
				"template" => "templates/base.php"
		)
);
