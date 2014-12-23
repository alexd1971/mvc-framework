<?php

namespace core;

/**
 * Класс Model.
 * Реализует базовый функционал для работы с данными
 * Модель всегда принадлежит Store и не может существовать отдельно.
 *
 * @author Алексей Данилевский
 *
 */
class Model {
	/*
	 * Данные не изменялись
	 */
	const UNCHANGED = 0;
	/*
	 * Добавлена новая запись
	 */
	const INSERT = 1;
	/*
	 * Данные изменены
	 */
	const UPDATE = 2;
	/*
	 * Данные удалены
	 */
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
	public function __construct($store = null) {
		if (is_a($store, 'Store') || $store === null){
			$this->_store = $store;
		}
		if ($this::$attributes){
			foreach ( $this::$attributes as $attribute ) {
				$this->_attributes [$attribute] = null;
			}
			$this->state = self::UNCHANGED;
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
		if (array_key_exists ( $attribute, $this->_attributes )) {
			return $this->_attributes [$attribute];
		} else {
			throw\Exception ( "Атрибут не найден" );
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
	/**
	 * "Волшебная" функция выполняется при попытке проверить наличия атрибута модели: isset($model->attribute);
	 * Возвращает ture, если атрибут существует, и false, если нет.
	 *
	 * @param string $attribute
	 * @return boolean
	 */
	public function __isset($attribute) {
		if (array_key_exists ( $attribute, $this->_attributes )) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Хранилище, ассоциированное с моделью
	 *
	 * @var Store object
	 */
	protected $_store;
	/**
	 * Хранилище значений атрибутов модели
	 *
	 * @var array
	 */
	protected $_attributes = array ();
}
