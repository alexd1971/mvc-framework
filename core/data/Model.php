<?php
namespace core\data;
use core\Framework;

/**
 * Класс Model.
 * Реализует базовый функционал для работы с данными
 * Модель всегда принадлежит Store и не может существовать отдельно.
 *
 * @author Aleksey.Danilevskiy
 *
 */
class Model {
	/*
	 * Данные не изменялись
	 */
	const UNCHANGED		= 0;
	/*
	 * Добавлена новая запись
	 */
	const INSERT 		= 1;
	/*
	 * Данные изменены
	 */
	const UPDATE		= 2;
	/**
	 * Список атрибутов модели с указанием допонительных параметров атрибутов, например, валидаторов
	 * Если список пустой, то выбираются все поля таблицы.
	 *
	 * Общий вид массива такой:
	 * 
	 * array(
	 * 		"attribute1" => array("param1","param2",...),
	 * 		"attribute2" => array(...),
	 * 		...
	 * );
	 * @var array
	 */
	public static $attributes = array();
	/**
	 * Имя атрибута, определяющего первичный ключ (идентификатор модели)
	 * 
	 * @var string
	 */
	public static $primaryKey = '';
	/**
	 * Состояние данных.
	 * Возможные варианты: Model::UNCHANGED, Model::INSERT, Model::UPDATE
	 *
	 * @var integer
	 */
	var $state;
	/**
	 * Конструктор класса.
	 *
	 * Если в конструкторе-класса наследника не определен список атрибутов, то конструктор выбирает
	 * в качестве атрибутов все доступные в таблице поля и инициализирует их значениями null.
	 *
	 */
	public function __construct($store){
		if($store instanceof Store){
			$this->store = $store;
			$class = get_class($this);
			if(!$class::$attributes){
				try{
					$dbh = Framework::application()->getDatabaseConnection($store->dbConnection);
					if($dbh){
						$res = $dbh->query ("select * from $store->table limit 0");
						for($i = 0; $i < $res->columnCount(); $i++){
							$columnInfo = $res->getColumnMeta($i);
							$class::$attributes[$columnInfo['name']] = null;
						}
					}
				}
				catch (\Exception $e){
					//TODO: Добавить обработку исключения
				}
			}

			foreach (array_keys($class::$attributes) as $attribute){
				$this->_attributes[$attribute] = null;
			}

			$this->state = self::INSERT;
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
	public function __get($attribute){
		if(array_key_exists($attribute, $this->_attributes)){
			return $this->_attributes[$attribute];
		}
		else {
			throw \Exception("Атрибут не найден");
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
		if(array_key_exists($attribute, $this->_attributes)){
			if($this->_attributes[$attribute] !== $value){
				$this->_attributes[$attribute] = $value;
				if($this->state == self::UNCHANGED){
					$this->state = self::UPDATE;
				}
			}
		}
		else {
			throw \Exception("Атрибут не найден");
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
		if(array_key_exists($attribute, $this->_attributes)){
			return true;
		}
		else {
			return false;
		}
	}
	/**
	 * Хранилище, ассоциированное с моделью
	 *
	 * @var Store object
	 */
	protected $store = null;
	/**
	 * Хранилище значений атрибутов модели
	 *
	 * @var array
	 */
	private $_attributes = array();
}
