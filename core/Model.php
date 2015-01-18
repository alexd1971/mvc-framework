<?php

namespace core;

/**
 * Класс Model.
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
	 * Состояние данных.
	 * Возможные варианты: Model::UNCHANGED, Model::INSERT, Model::UPDATE, Model::DELETE
	 *
	 * @var integer
	 */
	public $state;
	/**
	 * Конструктор класса.
	 *
	 * Аргумент $attributes является ассоциативным массивом, определяющим начальные значения атрибутов модели.
	 *
	 * Пример:
	 *
	 * array(
	 * "attr1" => 'val1',
	 * "attr2" => 'val2',
	 * ...
	 * "attrn" => null // если не нужно инициализировать атрибут, то его значение устанавливается в null
	 * )
	 *
	 * @param array $attributes        	
	 */
	public function __construct($attributes = array()) {
		$class = get_class ( $this );
		if ($class::$_attributes) {
			$this->state = self::UNCHANGED;
			foreach ( $class::$_attributes as $attribute ) {
				$this->_attrValues [$attribute] = (array_key_exists ( $attribute, $attributes ) ? $attributes [$attribute] : null);
			}
		} else {
			throw\Exception ( "В создаваемой модели " . $class . " не определено ни одного атрибута" );
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
			case "attributes" :
				$class = get_class ( $this );
				return $class::$_attributes;
			default :
				if (array_key_exists ( $attribute, $this->_attrValues )) {
					return $this->_attrValues [$attribute];
				} else {
					throw \Exception ( "Атрибут не найден" );
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
			default :
				if (array_key_exists ( $attribute, $this->_attrValues )) {
					if ($this->_attrValues [$attribute] !== $value) {
						$this->_attrValues [$attribute] = $value;
						if ($this->state == self::UNCHANGED) {
							$this->state = self::UPDATE;
						}
					}
				} else {
					throw \Exception ( "Атрибут не найден" );
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
			case "attributes" :
				return true;
			default :
				if (array_key_exists ( $attribute, $this->_attrValues )) {
					return true;
				} else {
					return false;
				}
		}
	}
	/**
	 * Список атрибутов модели
	 *
	 * @var array
	 */
	protected static $_attributes = array ();
	protected $_attrValues = array ();
}
