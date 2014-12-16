<?php
namespace core\data;
use core\Framework;

/**
 * Класс Model.
 * Реализует базовый функционал для работы с данными
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
	/*
	 * Типы отношения между моделями
	 */
	const HAS_ONE		= 1;
	const HAS_MANY		= 2;

	/**
	 * Имя подключения к БД.
	 * Параметры подключения определяются в конфигурации приложения
	 * По умолчанию используется подключение с именем 'db'.
	 * Если для модели, наследующей от данной, необходимо указать иное соединение с БД,
	 * то в классе этой модели нужно явно переопределить статическое свойство $connection
	 *
	 * @var string
	 */
	public static $connection = 'db';
	/**
	 * Имя таблицы БД
	 * Требуется явное переопределение в классах-наследниках
	 *
	 * @var string
	 */
	public static $table = '';
	/**
	 * Список атрибутов для выборки
	 * Если список пустой, то выбираются все поля таблицы.
	 * Требуется явное переопределение в классах-наследниках.
	 *
	 * @var array
	 */
	public static $attributes = array();
	/**
	 * Состояние данных.
	 * Возможные варианты: UNCHANGED, INSERT, UPDATE
	 *
	 * @var integer
	 */
	var $state;
	/**
	 * Отношения мжду моделями данных
	 * Типы отношений: HAS_ONE, HAS_MANY
	 *
	 * array(
	 * 	'relation_name' => array(self::HAS_MANY, 'model_name', 'relation_attribute'),
	 * );
	 *
	 * @var array
	 */
	var $relations = array();
	/**
	 * Конструктор класса.
	 *
	 * Если в конструкторе-класса наследника не определен список атрибутов, то конструктор выбирает
	 * в качестве атрибутов все доступные в таблице поля и инициализирует их значениями null.
	 *
	 */
	public function __construct(){

		if(!self::$attributes){
			try{
				$db = Framework::application()->dbConnection(self::$connection);
				if($db){
					$qr = $db->query ("select * from self::$table limit 0");
					for($i = 0; $i < $qr->columnCount(); $i++){
						$columnInfo = $qr->getColumnMeta($i);
						array_push(self::$attributes, $columnInfo['name']);
					}
				}
			}
			catch (\Exception $e){
				Framework::application()->dispatchException($e);
			}
		}

		foreach (self::$attributes as $attribute){
			$this->_attributes[$attribute] = null;
		}

		$this->state = self::INSERT;
	}
	/**
	 * Функция сохраняет значения атрибутов модели в БД, если они были изменены
	 */
	public function save(){

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
	 * Хранилище значений атрибутов модели
	 *
	 * @var array
	 */
	private $_attributes = array();
}
