<?php

namespace core;

class MVCF {
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
	public static $indexDir;
	/**
	 * Инициализайия фреймворка
	 */
	public static function initialize() {
		/*
		 * Читаем конфигурацию фреймворка
		 */
		self::$config = include 'config/config.php';
		/*
		 * Определяем корневой каталог
		 */
		preg_match ( '@^/(.*)/index.php$@', $_SERVER ['SCRIPT_NAME'], $matches );
		self::$indexDir = $_SERVER ['DOCUMENT_ROOT'] . '/' . ($matches ? $matches [1] : '');
		/*
		 * Если сконфигурированы дополнительные каталоги для поиска файлов, то добавляем их для поиска
		 */
		if (isset(self::$config['include_path'])){
			$path = implode ( PATH_SEPARATOR, self::$config ['include_path'] );
			set_include_path ( get_include_path () . PATH_SEPARATOR . $path );
		}
		/*
		 * Устанавливаем загрузчик классов
		 */
		include self::$indexDir . '/' . self::$config ['loader'];
		spl_autoload_register ( '\core\Loader::autoLoad' );
		/*
		 * Создаем новое приложение
		 */
		$appClass = '\\'.self::$config['appNamespace'].'\\Application';
		self::$_application = new $appClass;
	}
	/**
	 * Функция предоставляет доступ к приложению
	 */
	public static function app() {
		return self::$_application;
	}
	/**
	 * Экземпляр приложения
	 *
	 * @var \core\Application
	 */
	private static $_application;
	/**
	 * Запрещаем создание экземпляров класса MVCF
	 */
	private function __construct() {
	}
}