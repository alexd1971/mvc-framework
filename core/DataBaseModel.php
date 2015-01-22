<?php

namespace core;

/**
 * Базовый класс для моделей, получающих данные из БД
 *
 * @author Алексей Данилевский
 *
 */
abstract class DataBaseModel extends Model {
	/**
	 * public static function findByAttributes($criteria = array())
	 *
	 * Функция создает модели на основе записей в БД с указанными значеними атрибутов
	 * Если критериям соответствует единственная запись, то возвращает модель, содержащую данные из этой записи
	 * Если критериям соответствует несколько записей, то возвращвется массив моделей.
	 * Если ничего не найдено возвращает null
	 *
	 * Критерии представляют собой ассоциативный массив вида "атрибут" => "значение":
	 *
	 * array(
	 * 		"attr1" => val1,
	 * 		"attr2" => val2,
	 * 		...
	 * )
	 *
	 * @param array $criteria
	 * @return DataBaseModel
	 */
	public static function findByAttributes($criteria = array()) {
		$class = get_called_class ();
		$select = join ( ",\n", array_map(function($attr) {
			$class = get_called_class();
			return "{$class::$_alias}.$attr";
		}, array_keys($class::$_attributes) ));
		$where = "";
		foreach ( $criteria as $key => $value ) {
			if ($where !== "") {
				$where .= "\nand ";
			}
			$where .= "{$class::$_alias}.$key=" . (is_string ( $value ) ? "'$value'" : $value);
		}
		$sql = "select $select\nfrom {$class::$_table} {$class::$_alias}\n" . ($where?"where $where":"");
		return $class::findBySql($sql);
	}
	/**
	 * public static function find($criteria)
	 *
	 * Функция осуществляет выборку данных из БД в соответствии с критериями
	 * Возвращает массив моделей SqlDbModel
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
	 * @param array $criteria
	 * @return array
	 */
	public static function find($criteria) {
		$class = get_called_class ();
		$select = join ( ",\n", array_map(function($attr) {
			$class = get_called_class();
			return "{$class::$_alias}.$attr";
		}, array_keys($class::$_attributes) ));
		$where = $class::_compileCriteria ($criteria);
		$sql = "select $select\nfrom {$class::$_table} {$class::$_alias}\n" . ($where?"where $where":"");
		return $class::findBySql($sql);
	}
	/**
	 * public static function findBySql($sql)
	 *
	 * Функция конструирует модель на основании SQL-запроса
	 *
	 * @param string $sql
	 */
	public static function findBySql($sql) {
		$class = get_called_class ();
		$dbh = MVCF::app ()->getDBConnection ( $class::$_dbConnection );
		$stmt = $dbh->prepare ( $sql );
		$stmt->execute ();
		$models = array();
		if ($stmt->errorCode () == 0) {
			while($record = $stmt->fetch ( \PDO::FETCH_ASSOC )) {
				print_r($record);
				$model = new $class ( $record );
				$model->state = Model::UNCHANGED;
				$models[] = $model;
			}
		} else {
			$errors = $stmt->errorInfo ();
			echo ($errors [2]);
		}
		if($models){
			if (count($models) > 1){
				return $models;
			}
			else {
				return $models[0];
			}
		}
		else {
			return null;
		}
	}
	/**
	 * public static function sync(&$models)
	 *
	 * Функция синхронизирует переданный массив моделей с БД
	 * Синхронизируются только измененные модели
	 *
	 * @param array $models
	 */
	public static function sync(&$models) {
		$class = get_called_class();
		$insert = array();
		$update = array();
		$delete = array();
		foreach ($models as $model){
			switch($model->state) {
				case self::INSERT:
					$insert[] = $model;
					break;
				case self::UPDATE:
					$update[] = $model;
					break;
				case self::DELETE:
					$delete[] = $model;
					break;
			}
		}
		if ($insert || $update || $delete) {
			$dbh = MVCF::app ()->getDBConnection ( $class::$_dbConnection );
			$attributes = array_keys($class::$_attributes);
			$pk = array_search ( $class::$_primaryKey, $attributes );
			if ($pk !== false) {
				unset ( $attributes [$pk] );
			}
			if ($insert) {
				$sql = "insert into {$class::$_table} (" . join ( ',', $attributes ) . ") values (" . join ( ',', array_map ( function ($attribute) {
					return '?';
				}, $attributes ) ) . ")";
				$sth = $dbh->prepare ( $sql );
				foreach ( $insert as $model ) {
					$values = array ();
					foreach ( $attributes as $attribute ) {
						$values [] = $model->$attribute;
					}
					$sth->execute ( $values );
					if ($sth->errorCode () == 0) {
						if ($class::$_pkSequence) {
							$model->{$class::$_primaryKey} = $dbh->lastInsertId ( $class::$_pkSequence );
						} else {
							$model->{$class::$_primaryKey} = $dbh->lastInsertId ();
						}
						$model->state = Model::UNCHANGED;
					}
					else {
						$errors = $stmt->errorInfo ();
						echo ($errors [2]);
					}
				}
			}
			if ($delete) {
				$delList = array();
				foreach ($delete as $model){
					$delList[] = $model->{$class::$_primaryKey};
				}
				$sql = "delete from {$class::$_table} where {$class::$_primaryKey} in (" . join ( ',', $delList ) . ")";
				$sth = $dbh->prepare ( $sql );
				$sth->execute ();
				if ($sth->errorCode () == 0){
					foreach ($delete as $model) {
						$index = array_search($model, $models);
						if($index !== false) {
							unset($models[$index]);
						}
					}
				}
				else {
					$errors = $sth->errorInfo ();
					echo ($errors [2]);
				}

			}
			if ($update) {
				$sql = "update {$class::$_table} set " . join ( ',', array_map ( function ($attribute) {
					return $attribute . "=?";
				}, $attributes ) ) . " where {$class::$_primaryKey}=?";
				$sth = $dbh->prepare ( $sql );
				foreach ( $update as $model ) {
					$values = array ();
					foreach ( $attributes as $attribute ) {
						$values [] = $model->$attribute;
					}
					$values [] = $model->{$class::$_primaryKey};
					$sth->execute ( $values );
					if ($sth->errorCode () == 0) {
						$model->state = Model::UNCHANGED;
					}
					else {
						$errors = $sth->errorInfo ();
						echo ($errors [2]);
					}
				}
			}
		}
	}
	/**
	 * public function save()
	 *
	 * Функция сохраняет текущее состояние модели в БД
	 *
	 * Model::UNCHANGED - нмчего не делает
	 * Model::INSERT - добавляет новую запись в БД и устанавливает значение первичного ключа
	 * Model::UPDATE - сохраняет изменения модели в БД
	 * Model::DELETE - удаляет запись из БД
	 */
	public function save() {
		$class = get_class($this);
		$attributes = $this->attributes;
		$pk = array_search ( $class::$_primaryKey, $attributes );
		if ($pk !== false) {
			unset ( $attributes [$pk] );
		}

		$dbh = MVCF::app ()->getDBConnection ( $class::$_dbConnection );

		switch ($this->state) {
			case self::DELETE:
				$sql = "delete from {$class::$_table} where {$class::$_primaryKey}=" . $this->{$class::$_primaryKey};
				$sth = $dbh->prepare($sql);
				$sth->execute ();
				if ($sth->errorCode () == 0) {
					$this->{$class::$_primaryKey} = null;
					$this->state = Model::INSERT;
				}
				else {
					$errors = $sth->errorInfo ();
					echo ($errors [2]);
				}
				break;
			case self::UPDATE:
				$sql = "update {$class::$_table} set " . join ( ',', array_map ( function ($attribute) {
					return "$attribute=?";
				}, $attributes ) ) . " where {$class::$_primaryKey}=?";
				$values = array();
				foreach ($attributes as $attribute) {
					$values[] = $this->$attribute;
				}
				$values [] = $this->{$class::$_primaryKey};
				$sth = $dbh->prepare($sql);
				$sth->execute ( $values );
				if ($sth->errorCode () == 0) {
					$this->state = self::UNCHANGED;
				}
				else {
					$errors = $sth->errorInfo ();
					echo ($errors [2]);
				}
				break;
			case self::INSERT:
				$sql = "insert into {$class::$_table} (" . join ( ',', $attributes ) . ") values (" . join ( ',', array_map ( function ($attribute) {
					return '?';
				}, $attributes ) ) . ")";
				$values = array ();
				foreach ( $attributes as $attribute ) {
					$values [] = $this->$attribute;
				}
				$sth = $dbh->prepare($sql);
				$sth->execute ( $values );
				if ($sth->errorCode () == 0) {
					if ($class::$_pkSequence) {
						$this->{$class::$_primaryKey} = $dbh->lastInsertId ( $class::$_pkSequence );
					} else {
						$this->{$class::$_primaryKey} = $dbh->lastInsertId ();
					}
					$this->state = self::UNCHANGED;
				}
				else {
					$errors = $sth->errorInfo ();
					echo ($errors [2]);
				}
				break;
		}
	}
	/**
	 * public function delete($save = false)
	 * Функция помечает модель для удаления из БД
	 * Если параметр $save == true, то выполняется удаление соответствующей записи из БД.
	 * При этом модель утрачивает значение идентификатора и меняет статус на Model::INSERT
	 *
	 * @param boolean $save
	 */
	public function delete($save = false) {
		$this->state = self::DELETE;
		if ($save) {
			$this->save();
		}
	}
	/**
	 * PDO - объект соединения с БД
	 *
	 * @var PDO Object
	 */
	protected static $_dbConnection;
	/**
	 * Имя таблицы БД
	 *
	 * @var string
	 */
	protected static $_table;
	/**
	 * Псевдоним таблицы БД в запросах
	 *
	 * @var string
	 */
	protected static $_alias = "t";
	/**
	 * Имя поля первичного ключа
	 *
	 * @var string
	 */
	protected static $_primaryKey;
	/**
	 * Последовательность, используемая для автоинкремента (если поддерживается СУБД)
	 */
	protected static $_pkSequence;
	/**
	 * Функция возвращает подготовленную строку критериев выборки
	 *
	 * @return string
	 */
	protected static function _compileCriteria($criteria) {
		$class = get_called_class();
		if ($criteria) {
			return $class::_processCriteriaRecursive ( $criteria );
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
	protected static function _processCriteriaRecursive( $criteria ) {
		$criteriaString = '';
		$operator = strtolower ( $criteria [0] );
		$class = get_called_class();
		switch ($operator) {
			case 'and' :
			case 'or' :
				$criteriaString = $class::_processCriteriaRecursive ( $criteria [1] [0] );
				for($i = 1; $i < count ( $criteria [1] ); $i ++) {
					$criteriaString .= "\n$operator " . $class::_processCriteriaRecursive ( $criteria [1] [$i] );
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
				return "{$class::$_alias}." . $criteria [1] . " " . $criteria [0] . " " . $value;
			case 'is' :
				return "{$class::$_alias}." . $criteria [1] . " " . $criteria [0] . " " . $criteria [2];
			case 'between' :
				$arguments = array_map ( function ($value) {
					if (gettype ( $value ) === 'string') {
						$value = "'" . $value . "'";
					}
					return $value;
				}, $criteria [2] );
				return "{$class::$_alias}." . $criteria [1] . " " . $criteria [0] . " " . join ( " and ", $arguments );
			case 'in' :
			case 'not in' :
				$arguments = array_map ( function ($value) {
					if (gettype ( $value ) === 'string') {
						$value = "'" . $value . "'";
					}
					return $value;
				}, $criteria [2] );
				return "{$class::$_alias}." . $criteria [1] . " " . $criteria [0] . " (" . join ( ',', $arguments ) . ")";
			default :
				throw \Exception ( "Ошибка в формате критериев. Недопустимый оператор: $operator" );
		}
	}
}