<?php
namespace core;

class Framework{
	/**
	 * Конфигурация фреймворка
	 *
	 * Параметры загружаются в момент инициализации фреймворка.
	 * Конфигурация располагается в core/config/config.php
	 *
	 * @var array
	 */
	public static $config;
	/**
	 * Инициализайия фреймворка
	 */
	public static function initialize(){

		self::$config = include 'config/config.php';
		require_once $_SERVER['DOCUMENT_ROOT'].'/'.self::$config['loader'];
		spl_autoload_register('Loader::autoLoad');

	}
	/**
	 * Регистрация приложения
	 *
	 * @param \core\Application $application
	 * @throws \Exception
	 */
	public static function registerApplication($application){
		if($application instanceof Application){
			self::$_application = $application;
		}
		else{
			throw new \Exception("Попытка зарегистрировать недопустипое приложение");
		}
	}
	/**
	 * Функция предоставляет доступ к приложению
	 */
	public static function application(){
		return self::$_application;
	}

	private static $_application;

	private function __construct();

}