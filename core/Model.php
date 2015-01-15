<?php

namespace core;

/**
 * Класс ModelBase.
 * Базовый класс для создания моделей данных.
 * Наследники требуют явного определения $attributes и $idAttribute
 *
 * @author Алексей Данилевский
 *
 */
abstract class Model {
	/**
	 * Константы, определяющие состояние модели
	 * 
	 * UNCHANGED - данные не изменялись
	 * INSERT - новые данные
	 * UPDATE - данные изменились
	 * DELETE - удалить данные при синхронизации с хранилищем
	 */
	const UNCHANGED = 0;
	const INSERT = 1;
	const UPDATE = 2;
	const DELETE = 3;
	/**
	 * Список атрибутов модели
	 * Если список пустой, то выбираются все поля таблицы.
	 *
	 * Общий вид массива такой:
	 *
	 * array(
	 * "attribute1",
	 * "attribute2",
	 * ...
	 * );
	 *
	 * @var array
	 */
	public static $attributes = array ();
	/**
	 * Имя атрибута, определяющего идентификатор модели
	 *
	 * @var string
	 */
	public static $idAttribute = '';
	/**
	 * Состояние данных.
	 * Возможные варианты: Model::UNCHANGED, Model::INSERT, Model::UPDATE, Model::DELETE
	 *
	 * @var integer
	 */
	public $state;
	/**
	 * Конструктор класса.
	 *
	 * Если в конструкторе-класса наследника не определен список атрибутов, то конструктор выбирает
	 * в качестве атрибутов все доступные в таблице поля и инициализирует их значениями null.
	 */
	public function __construct($attributes = array()) {
		if ($this::$attributes){
			$this->state = self::INSERT;
			foreach ( $this::$attributes as $attribute ) {
				$this->_attributes [$attribute] = key_exists($attribute, $attributes)?$attributes[$attribute]:null;
			}
		}
		else {
			throw \Exception ("В создаваемой модели ".get_class($this)." не определено ни одного атрибута");
		}
	}
	/**
	 * "Волшебная" функция возвращает значение атрибута, если он определен.
	 * Если атрибут не определен, срабатывает исключение
	 *
	 * Функция вызывается не явно при попытке получить значение атрибута модели: $model->attribute;
	 *
	 * @param string $attribute
	 * @return multitype:
	 */
	public function __get($attribute) {
		switch ($attribute) {
			default:
				if (array_key_exists ( $attribute, $this->_attributes )) {
					return $this->_attributes [$attribute];
				} else {
					throw\Exception ( "Атрибут не найден" );
				}
		}
	}
	/**
	 * "Волшебная" функция устанавливает значение атрибута, если он определен
	 * Если атрибут не определен, срабатывает исключение
	 *
	 * Функция вызывается не явно при попытке установить значение атрибута модели: $model->attribute = $value;
	 *
	 * @param string $attribute
	 * @param mixed $value
	 */
	public function __set($attribute, $value) {
		switch ($attribute) {
			default:
				if (array_key_exists ( $attribute, $this->_attributes )) {
					if ($this->_attributes [$attribute] !== $value) {
						$this->_attributes [$attribute] = $value;
						if ($this->state == self::UNCHANGED) {
							$this->state = self::UPDATE;
							if ($this->_store){
								$this->_store->updated [] = $this;
							}
						}
					}
				} else {
					throw\Exception ( "Атрибут не найден" );
				}
		}
	}
	/**
	 * "Волшебная" функция выполняется при попытке проверить наличия атрибута модели: isset($model->attribute);
	 * Возвращает ture, если атрибут существует, и false, если нет.
	 *
	 * @param string $attribute
	 * @return boolean
	 */
	public function __isset($attribute) {
		switch ($attribute) {
			default:
				if (array_key_exists ( $attribute, $this->_attributes )) {
					return true;
				} else {
					return false;
				}
		}
	}
	/**
	 * Хранилище значений атрибутов модели
	 *
	 * @var array
	 */
	protected $_attributes = array ();
}
