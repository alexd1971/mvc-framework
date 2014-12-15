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

		$config = Framework::$config;
		$file = implode('/',explode('\\', $class)) . '.php';
		if(file_exists($file)){
			include $file;
		}
//TODO: Изменить код, чтобы не выполнялся include несуществующего файла
		else{
			$path = implode(PATH_SEPARATOR, $config['include_path']);
			set_include_path(get_include_path() . PATH_SEPARATOR . $path);
			include $class.'.php';
		}

	}
}