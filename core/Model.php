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
	 * Аргумент $attributes является ассоциативным массивом, определяющим начальные значения атрибутов.
	 *
	 * Пример:
	 *
	 * array(
	 * 		"attr1" => value1,
	 * 		"attr2" => value2,
	 * 		...
	 * )
	 *
	 * Если конструктору переданы атрибуты, то модель переходит в статус Model::INSERT
	 *
	 * @param array $attributes
	 */
	public function __construct($attributes = array()) {
		$class = get_class ( $this );
		if ($class::$_attributes) {
			$this->state = self::BLANK;
			foreach ( array_keys($class::$_attributes) as $attribute ) {
				$this->$attribute = (array_key_exists ( $attribute, $attributes ) ? $attributes [$attribute] : null);
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
		$validators = MVCF::app()->validators;
		if ($this->_isValid == self::NOT_VALIDATED) {
			$class = get_class($this);
			foreach ($class::$_attributes as $attribute => $config) {
				if (isset($config['validators']) && $config['validators']) {
					foreach ($config['validators'] as $key => $value) {
						$validatorClass = '';
						if(key_exists($key, $validators)){
							$validatorClass = $validators[$key];
						}
						else {
							throw new \Exception("Валидатор '$key' не найден");
						}

						$validator = new $validatorClass;
						$params = is_array($value)? $value:array();
						$params['attribute'] = $attribute;
						$params['model'] = $this;
						$result = $validator->check($params);
						$this->_validationResults[$attribute]['valid'] = $result;
						if ($result === false) {
							$this->_validationResults[$attribute]['message'] = isset($value['message'])?$value['message']:"Неверное значение атрибута '$attribute'";
							if ($this->_isValid != self::INVALID) {
								$this->_isValid = self::INVALID;
							}
							break;
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
		if ($this->_isValid == self::NOT_VALIDATED){
			$this->validate();
		}
		return $this->_isValid == self::VALID?true:false;
	}
	/**
	 * Возвращает результаты валидации модели
	 *
	 * @return multitype:
	 */
	public function getValidationResults (){
		return $this->_validationResults;
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
		$class = get_class ( $this );
		switch ($attribute) {
			case "attributes" :
				return array_keys($class::$_attributes);
			default :
				if (array_key_exists ( $attribute, $this->_attrValues )) {
					$value = $this->_attrValues [$attribute];
					if (isset($class::$_attributes[$attribute]['filters']['get'])) {
						try {
							$filter = $class::$_attributes[$attribute]['filters']['get'];
							$value = $this->$filter($value);
						}
						catch (\Exception $e)  {
							die ("Фильтр get ('$filter') для атрибута '$attribute' не определен");
						}
					}
					return $value;
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
		$class = get_class($this);
		switch ($attribute) {
			default :
				if (array_key_exists ( $attribute, $class::$_attributes )) {
					if (isset($class::$_attributes[$attribute]['filters']['set'])) {
						try {
							$filter = $class::$_attributes[$attribute]['filters']['set'];
							$value = $this->$filter($value);
						}
						catch (\Exception $e)  {
							die ("Фильтр set ('$filter') для атрибута '$attribute' не определен");
						}
					}
					if (!isset($this->_attrValues [$attribute]) || $this->_attrValues [$attribute] !== $value) {
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
	 * Функция устанавливает значения атрибутов модели на основании данных ассоциативного массива $attributes
	 * Массив значений имеет вид аналогичный описанному в комментариях к конструктору
	 *
	 * @param array $values
	 */
	public function setAttributes( $attributes ) {
		$class = get_class ( $this );
		if ($attributes) {
			foreach ( array_keys($class::$_attributes) as $attribute ) {
				if (array_key_exists ( $attribute, $attributes )) {
					$this->$attribute = $attributes [$attribute];
				}
			}
		}
	}
	/**
	 * Функция возвращает значение атрибута без обработки фильтрами, если таковые для данного атрибута сконфигурированы
	 *
	 * @param string $attribute
	 * @throws \Exception
	 * @return multitype:
	 */
	public function getRawAttribute($attribute) {
		if (array_key_exists ( $attribute, $this->_attrValues )) {
			$value = $this->_attrValues [$attribute];
			return $value;
		} else {
			throw new \Exception ( "Атрибут не найден: $attribute" );
		}
	}

	public function setRawAttribute($attribute, $value) {
		if (array_key_exists($attribute, $this::$_attributes)){
			$this->_attrValues[$attribute] = $value;
		} else {
			throw new \Exception ( "Атрибут не найден: $attribute" );
		}
	}
	/**
	 * Описание атрибутов модели с валидаторами для проверки соответствия значений атрибутов  требованиям
	 * Валидаторы должны реализовывать интерфейс IValidator
	 *
	 * Общий вид описания атрибутов приведен ниже. Помимо параметров, описанных в массиве, валидатору передается дополнительно параметр "attribute",
	 * содержащий название атрибута и параметр "model", содержащий ссылку на модель данных. Параметр "message" определяет текст сообщения в случае,
	 * если атрибут имеет недопустимое значение.
	 *
	 * Если параметр "message" не указан, то в случае недопустимости значения атрибута валидатор вернет сообщение, определенное
	 * в самом валидаторе.
	 *
	 * Проверка допустимости атрибута прекращается, как только встречается первое невыполнившееся правило
	 *
	 * array(
	 * 		"attr1" => array (
	 * 			"validators" => array(
	 * 				"validator1" => array("param1" => value1, "param2" => value2,..., "message" => "Сообщение об ошибке"),
	 * 				"validator2" => array(...)
	 * 			),
	 * 			"filters" => array(
	 * 				"get" => "function_get",
	 * 				"set" => "function_set"
	 * 			)
	 * 		),
	 * 		"attr2" => array(...),
	 * 		...
	 * 		"attrn" => array() // если не нужно описывать атрибут, то его значение устанавливается в array()
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
