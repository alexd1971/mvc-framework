<?php

namespace core;

class Request {
	/**
	 * Параметры запроса
	 *
	 * @var array
	 */
	var $params;
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

		foreach ($_SERVER as $key => $value) {
			$key = strtolower($key);
			$this->$key = $value;
		}
		preg_match ( '@^(.*)index\.php$@', $this->script_name, $matches );
		preg_match ( "@^$matches[1]([^.&?]*)$@", $this->request_uri, $matches );

		$params = explode ( '/', $matches ? $matches [1] : '' );

		if ($params) {
			$this->params = $params;
		}
		foreach ($_REQUEST as $key => $value) {
			$this->$key = $value;
		}

	}
}