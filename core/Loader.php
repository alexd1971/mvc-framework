<?php

namespace core;

/**
 * Класс Loader
 *
 * Предоставляет функцию для автоматической загрузки классов
 *
 * @author Алексей Данилевский
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
	static function autoLoad($class) {
		$config = MVCF::$config;
		$file = implode ( '/', explode ( '\\', $class ) ) . '.php';
		if (file_exists ( $file )) {
			include $file;
		}
	}
}