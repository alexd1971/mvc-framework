<?php
namespace core;
/**
 * Класс Model.
 * Реализует базовый функционал для работы с данными
 *
 * @author Aleksey.Danilevskiy
 *
 */
class Model {
	/**
	 * Имя подключения к БД.
	 * Параметры подключения определяются в конфигурации приложения
	 * По умолчанию используется подключение с именем 'db'
	 *
	 * @var string
	 */
	var $connection = 'db';
	/**
	 * Имя таблицы БД
	 *
	 * @var string
	 */
	var $table = '';
	/**
	 * Список атрибутов для выборки
	 * Если список пустой, то выбираются все поля таблицы
	 *
	 * @var array
	 */
	var $select = array();
	/**
	 * Поиск всех записей по установленным критериям
	 *
	 * @param array $criteria
	 */
	public static function findAll($criteria = array()){

	}
	/**
	 * Поиск записей по SQL-запросу
	 *
	 * @param string $sql
	 */
	public static function findBySQL($sql){

	}

}
