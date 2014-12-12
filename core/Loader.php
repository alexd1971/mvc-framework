<?php
namespace core;
/**
 * Класс Loader
 *
 * Предоставляет функцию для автоматической загрузки классов
 *
 * @author Aleksey.Danilevskiy
 *
 */
class Loader {
	/**
	 * Статическая функция autoLoad
	 *
	 * Осуществляет автоматическую загрузку класса
	 *
	 * @param string $class
	 */
	static function autoLoad($class){

		global $config;
		$file = implode('/',explode('\\', $class)) . '.php';
		if(file_exists($file)){
			include $file;
		}
		else{
			$path = implode(PATH_SEPARATOR, $config['classpath']);
			set_include_path(get_include_path() . PATH_SEPARATOR . $path);
			include $class.'.php';
		}

	}
}