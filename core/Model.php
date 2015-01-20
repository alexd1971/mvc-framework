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
	const NEW_MODEL	= 0;
	const UNCHANGED = 1;
	const INSERT	= 2;
	const UPDATE	= 3;
	const DELETE	= 4;
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
			$this->state = self::NEW_MODEL;
			foreach ( array_keys($class::$_attributes) as $attribute ) {
				$this->_attrValues [$attribute] = (array_key_exists ( $attribute, $attributes ) ? $attributes [$attribute] : null);
				if($this->$attribute !== null) {
					$this->state = self::INSERT;
				}
			}
		} else {
			throw\Exception ( "В создаваемой модели " . $class . " не определено ни одного атрибута" );
		}
	}

	public function isValid() {
		// TODO: Добавить реализаци функции
		return true;
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
				return array_keys($class::$_attributes);
			default :
				if (array_key_exists ( $attribute, $this->_attrValues )) {
					return $this->_attrValues [$attribute];
				} else {
					throw new \Exception ( "Атрибут не найден: $attribute" );
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
						switch ($this->state) {
							case self::NEW_MODEL:
								$this->state = self::INSERT;
								break;
							case self::UNCHANGED:
								$this->state = self::UPDATE;
								break;
						}
					}
				} else {
					throw new \Exception ( "Атрибут не найден: $attribute" );
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
	 * Описание атрибутов модели с валидаторами для проверки соответствия значений атрибутов  требованиям
	 * Валидаторы должны реализовывать интерфейс IValidator
	 *
	 * array(
	 *
	 * 		"attribute1" => array(
	 * 			array("\validator\Class1", array("param1" => value1, "param2 => value2, ...)),	// Первое правило валидации для атрибута
	 * 			array("\validator\Class2", array("param3" => value3, "param4 => value4, ...)),	// Второе правило валидации для атрибута
	 * 			...
	 * 		),
	 *
	 * 		"atribute2" => array(
	 * 			array(...),
	 * 			...
	 * 		)
	 * )
	 *
	 * @var array
	 */
	protected static $_attributes = array ();
	/**
	 * Значения атрибутов модели
	 *
	 * @var array
	 */
	protected $_attrValues = array ();
}
