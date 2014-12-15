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
	 * Стартовый каталог, в котором находится index.php приложения
	 * 
	 * @var string
	 */
	public static $index_base;
	/**
	 * Инициализайия фреймворка
	 */
	public static function initialize(){

		self::$config = include 'config/config.php';
		preg_match('@^/(.*)/index.php$@', $_SERVER['SCRIPT_NAME'], $matches);
		self::$index_base = $_SERVER['DOCUMENT_ROOT'].'/'.($matches?$matches[1]:'');
		include self::$index_base.'/'.self::$config['loader'];
		spl_autoload_register('\core\Loader::autoLoad');

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

	private function __construct(){}

}