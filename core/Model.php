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
	const BLANK	= 0;
	const UNCHANGED = 1;
	const INSERT	= 2;
	const UPDATE	= 3;
	const DELETE	= 4;
	/**
	 * Константы, определяющие допустимость значений атрибутов модели
	 *
	 * NOT_VALIDATED	- проверка не проводилась
	 * VALID			- все атрибуты имеют допустимые значения
	 * INVALID			- по крайней мере один из атрибутов имеет недопустимое значение
	 */
	const NOT_VALIDATED	= 0;
	const VALID			= 1;
	const INVALID		= 2;
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
			$this->state = self::BLANK;
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
	/**
	 * Функция выполняет проверку атрибутов модели на соответствие требованиям описанным в $_attributes
	 * Возвращает массив с результатами проверки:
	 *
	 * array (
	 * 		"attribute1" => array(
	 * 			"valid" => true
	 * 		),
	 * 		"attribute2" => array(
	 * 			"valid" => false,
	 * 			"message"=>	"Не допустимое значение атрибута"
	 * 		),
	 * 		...
	 * )
	 *
	 * Если хотя бы один из атрибутов не проходит проверку, то устанавливается признак $_isValid = Model::INVALID
	 * Доступ к признаку $_isValid можно получить посредством функции isValid()
	 *
	 * Проверка валидности атрибута прекарщается на первом же правиле вернувшем false, после чего переходит на проверку следующего атрибута.
	 *
	 * @return array
	 */
	public function validate() {
		if ($this->_isValid == self::NOT_VALIDATED) {
			$class = get_class($this);
			foreach ($class::$_attributes as $attribute => $rules) {
				if ($rules) {
					foreach ($rules as $rule) {
						$validator = new $rule['validator'];
						$params = array_key_exists('params', $rule)? $rule['params']:array();
						$params['value'] = $this->$attribute;
						$validationResult = $validator->check($params);
						$this->_validationResults[$attribute] = $validationResult;
						if (!$validationResult["valid"] && $this->_isValid != self::INVALID) {
							$this->_isValid = self::INVALID;
						}
					}
				}
			}
			if($this->_isValid != self::INVALID) {
				$this->_isValid = self::VALID;
			}
		}
		return $this->_validationResults;
	}
	/**
	 * Функция возвращает значение признака допустимости атрибутов модели.
	 * Если проверка на допустимость не проводилась (значение признака $_isValid == Model::NOT_VALIDATED), то автоматически производится
	 * проверка модели (validate()) и возвращается результат этой проверки.
	 *
	 * @return boolean
	 */
	public function isValid() {
		if ($this->_isValid === self::NOT_VALIDATED){
			$this->validate();
		}
		return $this->_isValid == self::VALID?true:false;
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
							case self::BLANK:
								$this->state = self::INSERT;
								break;
							case self::UNCHANGED:
								$this->state = self::UPDATE;
								break;
						}
						if ($this->_isValid != self::NOT_VALIDATED) {
							$this->_isValid = self::NOT_VALIDATED;
							$this->_validationResults = array();
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
	 * Общий вид описания атрибутов приведен ниже. Помимо параметров, описанных в массиве, валидатору передается дополнительно параметр "value",
	 * содержащий значение атрибута. Параметр "message" определяет текст сообщения в случае, если атрибут имеет недопустимое значение.
	 * Если параметр "message" не указан, то в случае недопустимости значения атрибута валидатор вернет сообщение, определенное
	 * в самом валидаторе.
	 * Проверка допустимости атрибута прекращается, как только встречается первое невыполнившееся правило
	 *
	 * array(
	 *
	 * 		"attribute1" => array(
	 * 			array(
	 * 				"validator" => '\validator\Class1',
	 * 				"params" => array(
	 * 					"param1" => value1,
	 * 					"param2 => value2,
	 * 					...
	 * 				)
	 * 			),	// Первое правило валидации для атрибута
	 *
	 * 			array(
	 * 				"validator" => '\validator\Class2',
	 * 				"params" => array(
	 * 					"param3" => value3,
	 * 					"message" => "Информационное сообщение для пользователя",
	 * 					...
	 * 				)
	 * 			),	// Второе правило валидации для атрибута
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
	/**
	 * Состояние допустимости значений атрибутов модели
	 * Допустимые значения:
	 *
	 * Model::NOT_VALIDATED
	 * Model::VALID
	 * Model::INVALID
	 *
	 * @var integer
	 */
	protected $_isValid = self::NOT_VALIDATED;
	/**
	 * Результаты проверки допустимости атрибутов модели
	 * Значение устанавливается функцией validate()
	 * Массив содержит данные следующего вида:
	 *
	 * array(
	 * 		"attribute1" => array(
	 * 			"valid" => true
	 * 		),
	 * 		"attribute2" => array(
	 * 			"valid" => false,
	 * 			"message" => "Параметр не является строкой символов"
	 * 		)
	 * )
	 * @var array
	 */
	protected $_validationResults = array();
}
