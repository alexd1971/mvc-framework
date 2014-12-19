<?php

namespace core\http;

class Request {
	/**
	 * Запрошенный контроллер
	 *
	 * @var string
	 */
	var $controller = '';
	/**
	 * Запрошеное действие
	 *
	 * @var string
	 */
	var $action = '';
	/**
	 * Аргументы для выполнения действия
	 *
	 * @var array
	 */
	var $arguments = array ();

	/**
	 * Singletone
	 *
	 * @var Object
	 */
	protected static $_instance = null;
	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self ();
		}
		return self::$_instance;
	}
	/**
	 * Выделяем из запроса параметры, определяющие контроллер, действие и аргументы для действия
	 */
	private function __construct() {
		$request_uri = $_SERVER ['REQUEST_URI'];
		$script_name = $_SERVER ['SCRIPT_NAME'];
		preg_match ( '@^(/.*/)index\.php$@', $script_name, $matches );
		preg_match ( "@^$matches[1]([^.&?]*)$@", $request_uri, $matches );
		$params = explode ( '/', $matches ? $matches [1] : '' );
		if ($params) {
			$this->controller = isset ( $params [0] ) ? $params [0] : '';
			$this->action = isset ( $params [1] ) ? $params [1] : '';
			$this->arguments = array_slice ( $params, 2 );
		}
	}
}