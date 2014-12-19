<?php

namespace core\data;

use core\Framework;

class Store {
	/**
	 * Имя класса модели данных
	 *
	 * @var string
	 */
	public $model = '';
	/**
	 * Имя соединения с БД
	 *
	 * @var
	 *
	 */
	public $dbConnection = '';
	/**
	 * Имя таблицы БД
	 *
	 * @var string
	 */
	public $table = '';
	/**
	 * Псевдоним таблицы БД
	 *
	 * @var string
	 */
	public $alias = '';
	/**
	 * Имя последовательности для получения id записи (Нужно указывать, если поддерживается БД)
	 *
	 * @var string
	 */
	public $idSequence = '';
	/**
	 * var $criteria = array();
	 *
	 * Критерии выборки данных
	 * Формат определения критериев выборки соответствует предложению "where" sql-запроса.
	 *
	 * Одиночный критерий представляет собой массив, нулевой элемент которого представляет собой оператор, а остальные элементы - аргументы.
	 * Примеры критериев:
	 *
	 * array('=', "attribute1", $value1),
	 * array('like', "attribute2", "%$val%"),
	 * array('between', "attribute3", array($val1, $val2)),
	 * array('in', "attribute4", array($val1, $val2, $val3, ...))
	 * array('is', "attribute5", 'not null')
	 *
	 * Для объединения критерии логическими связками "and" и "or":
	 *
	 * array("and", $criteria) // $criteria - массив критериев, которые нужно объединить связкой "and"
	 * array("or", $criteria) // $criteria - массив критериев, которые нужно объединить связкой "or"
	 *
	 * Для объединения критериев в примере выше логической связкой "and" получаем:
	 *
	 * array('and', array(
	 * array('=', "attribute1", $value1),
	 * array('like', "attribute2", "%$val%"),
	 * array('between', "attribute3", array($val1, $val2)),
	 * array('in', "attribute4", array($val1, $val2, $val3, ...)),
	 * array('is', "attribute5", 'not null'),
	 * array('or', array(
	 * array('=', "attribute6", $value6),
	 * array(...),
	 * ...
	 * ),
	 * ));
	 *
	 * @var array
	 */
	public $criteria = array ();
	/**
	 * Размер страницы для загрузки данных.
	 * Значение 0 не ограничивает размер страницы.
	 *
	 * @var integer
	 */
	public $pageSize = 0;
	/**
	 * Массив моделей для изменения
	 *
	 * @var array
	 */
	public $updated = array ();
	/**
	 * Массив моделей для вставки
	 *
	 * @var array
	 */
	public $inserted = array ();
	/**
	 * Массив моделей для удаления
	 *
	 * @var array
	 */
	public $deleted = array ();
	/**
	 * Функция загружает данные в хранилище в соответствии с установленными критериями
	 */
	public function load() {
		$dbh = Framework::application ()->getDatabaseConnection ( $this->dbConnection );
		if ($dbh) {
			$modelClass = $this->model;
			$attributes = $modelClass::$attributes ? "\n" . join ( ",\n", array_keys ( $modelClass::$attributes ) ) : '*';
			$criteria = $this->_getCriteriaAsString ();
			$sql = "select $attributes\nfrom $this->table $this->alias";
			$sql .= $criteria ? "\nwhere $criteria" : "";
			$res = $dbh->query ( $sql );
			if ($res) {
				while ( $record = $res->fetch ( \PDO::FETCH_ASSOC ) ) {
					$model = new $modelClass ( $this );
					foreach ( $model::$attributes as $attribute ) {
						$model->$attribute = $record [$attribute];
					}
					$this->_data [] = $model;
					$model->state = Model::UNCHANGED;
				}
			}
		}
	}
	/**
	 * Функция сохраняет измененные данные в БД
	 */
	public function save() {
		if ($this->inserted || $this->updated || $this->deleted) {
			$dbh = Framework::application ()->getDatabaseConnection ( $this->dbConnection );
			$modelClass = $this->model;
			$attributes = $modelClass::$attributes;
			$pk = array_search ( $modelClass::$primaryKey, $attributes );
			if ($pk !== false) {
				unset ( $attributes [$pk] );
			}
			if ($this->inserted) {
				$sql = "insert into $this->table (" . join ( ',', $attributes ) . ") values (" . join ( ',', array_map ( function ($attribute) {
					return '?';
				}, $attributes ) ) . ")";
				$sth = $dbh->prepare ( $sql );
				foreach ( $this->inserted as $model ) {
					$values = array ();
					foreach ( $attributes as $attribute ) {
						$values [] = $model->$attribute;
					}
					try {
						$sth->execute ( $values );
						if ($this->idSequence) {
							$model->{$model::$primaryKey} = $dbh->lastInsertId ( $this->idSequence );
						} else {
							$model->{$model::$primaryKey} = $dbh->lastInsertId ();
						}
					} catch ( \Exception $e ) {
						// TODO: Добавть обработчик исключения
						echo $e->getMessage ();
					}
				}
				$this->inserted = array ();
			}
			if ($this->deleted) {
				$sql = "delete from $this->table where " . $modelClass::$primaryKey . " in (" . join ( ',', array_map ( function ($model) {
					return $model->{$model::$primaryKey};
				}, $this->deleted ) ) . ")";
				try {
					$dbh->exec ( $sql );
					$this->deleted = array ();
				} catch ( \Exception $e ) {
					// TODO: Добавть обработчик исключения
				}
			}
			if ($this->updated) {
				$sql = "update $this->table set " . join ( ',', array_map ( function ($attribute) {
					return $attribute . "=?";
				}, $attributes ) ) . " where " . $modelClass::$primaryKey . "=?";
				$sth = $dbh->prepare ( $sql );
				try {
					foreach ( $this->updated as $model ) {
						$values = array ();
						foreach ( $attributes as $attribute ) {
							$values [] = $model->$attribute;
						}
						$values [] = $model->{$model::$primaryKey};
						$sth->execute ( $values );
						$model->state = Model::UNCHANGED;
					}
					$this->updated = array ();
				} catch ( \Exception $e ) {
					// TODO: Добавить обработчик исключения
				}
			}
		}
	}
	/**
	 * Возвращает модель по известному идентификатору (значению первичного ключа)
	 * Если модели с указанным id нет, то возвращает null
	 *
	 * @param multitype $id
	 * @return <NULL, Model>
	 */
	public function getByPrimaryKey($id) {
		$model = null;
		foreach ( $this->data as $model ) {
			if ($model->{$model::$primaryKey} == $id)
				break;
		}
		return $model;
	}
	/**
	 * Функция добавляет новую модель в хранилище
	 * Возвращает ссылку на вновь созданный экземпляр модели данных
	 *
	 * @param array $attributes
	 * @return Model
	 */
	public function add($attributes = array()) {
		$modelClass = $this->model;
		$model = new $modelClass ( $this );
		if ($attributes) {
			foreach ( $attributes as $attribute => $value ) {
				try {
					$model->$attribute = $value;
				} catch ( \Exception $e ) {
					// TODO: Возможно нужно добавить обработку этого исключения.
					// Без обработки несуществующие атрибуты молча пропускаются
					echo $e->getMessage ();
				}
			}
		}
		$this->_data [] = $model;
		$this->inserted [] = $model;
		$model->state = Model::INSERT;
		return $model;
	}
	public function delByPrimaryKey($id) {
		foreach ( $this->data as $key => $model ) {
			if ($model->{$model::$primaryKey} == $id) {
				$model->state = Model::DELETE;
				$this->deleted [] = $model;
				unset ( $this->_data [$key] );
			}
		}
	}
	/**
	 * Функция предоставляет доступ к некоторым явно не определенным свойствам класса.
	 * В настоящий момент это только свойство 'data' - итератор для обхода всех моделей хранилища.
	 * Пример использования:
	 *
	 * foreach ($store->data as $model){
	 * do somthing...
	 * }
	 *
	 * @param string $property
	 * @return \ArrayIterator
	 */
	public function __get($property) {
		switch ($property) {
			case 'data' :
				return new \ArrayIterator ( $this->_data );
			default :
				$class = get_class ( $this );
				throw\Exception ( "Свойство $class::$property не найдено" );
		}
	}
	/**
	 * Функция возвращает подготовленную строку критериев выборки
	 *
	 * @return string
	 */
	protected function _getCriteriaAsString() {
		if ($this->criteria) {
			return $this->_processCriteriaRecursive ( $this->criteria );
		} else {
			return '';
		}
	}
	/**
	 * Функция рекурсивно проходит массив критериев и возвращает строку критериев
	 *
	 * @param array $criteria
	 * @return string
	 */
	protected function _processCriteriaRecursive($criteria) {
		$criteriaString = '';
		$operator = strtolower ( $criteria [0] );

		switch ($operator) {
			case 'and' :
			case 'or' :
				$criteriaString = $this->_processCriteriaRecursive ( $criteria [1] [0] );
				for($i = 1; $i < count ( $criteria [1] ); $i ++) {
					$criteriaString .= "\n$operator " . $this->_processCriteriaRecursive ( $criteria [1] [$i] );
				}
				return "(\n" . $criteriaString . "\n)";
			case '=' :
			case '>' :
			case '<' :
			case '!=' :
			case '>=' :
			case '<=' :
			case 'like' :
				$value = $criteria [2];
				if (gettype ( $value ) === 'string') {
					$value = "'" . $value . "'";
				}
				return "$this->alias." . $criteria [1] . " " . $criteria [0] . " " . $value;
			case 'is' :
				return "$this->alias." . $criteria [1] . " " . $criteria [0] . " " . $criteria [2];
			case 'between' :
				$arguments = array_map ( function ($value) {
					if (gettype ( $value ) === 'string') {
						$value = "'" . $value . "'";
					}
					return $value;
				}, $criteria [2] );
				return "$this->alias." . $criteria [1] . " " . $criteria [0] . " " . join ( " and ", $arguments );
			case 'in' :
			case 'not in' :
				$arguments = array_map ( function ($value) {
					if (gettype ( $value ) === 'string') {
						$value = "'" . $value . "'";
					}
					return $value;
				}, $criteria [2] );
				return "$this->alias." . $criteria [1] . " " . $criteria [0] . " (" . join ( ',', $arguments ) . ")";
			default :
				throw\Exception ( "Ошибка в формате критериев. Недопустимый оператор: $operator" );
		}
	}
	/**
	 * Хранилище данных
	 *
	 * @var array of Model
	 */
	protected $_data = array ();
}