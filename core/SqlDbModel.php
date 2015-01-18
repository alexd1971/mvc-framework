<?php

namespace core;

/**
 * Базовый класс для моделей, получающих данные из БД
 *
 * @author Алексей Данилевский
 *        
 */
abstract class SqlDbModel extends Model {
	/**
	 * Функция возвращает первую найденную в БД модель с указанными значеними атрибутов
	 *
	 * @param array $criteria        	
	 * @return SqlDbModel
	 */
	public static function findByAttributes($criteria) {
		$class = get_called_class ();
		$model = null;
		$select = join ( ",\n", $class::$_attributes );
		$where = "";
		foreach ( $criteria as $key => $value ) {
			if ($where !== "") {
				$where .= "\nand ";
			}
			$where .= $key . "=" . (is_string ( $value ) ? "'$value'" : $value);
		}
		$sql = "select $select\nfrom {$class::$_table}\nwhere $where limit 1";
		$dbh = MVCF::app ()->getDBConnection ( $class::$_dbConnection );
		$stmt = $dbh->prepare ( $sql );
		$stmt->execute ();
		if ($stmt->errorCode () == 0) {
			$record = $stmt->fetch ( \PDO::FETCH_ASSOC );
			$model = new $class ( $record );
		} else {
			$errors = $stmt->errorInfo ();
			echo ($errors [2]);
		}
		return $model;
	}
	/**
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
	public static function findAll($criteria) {
	}
	/**
	 * Функция конструирует модель на основании SQL-запроса
	 *
	 * @param string $sql        	
	 */
	public static function findBySql($sql) {
	}
	/**
	 * Функция синхронизирует переданный массив моделей с БД
	 * Синхронизируются только измененные модели
	 *
	 * @param array $models        	
	 */
	public static function sync($models) {
	}
	/**
	 * Функция сохраняет текущее состояние модели в БД
	 *
	 * Model::UNCHANGED - нмчего не делает
	 * Model::INSERT - добавляет новую запись в БД и устанавливает значение первичного ключа
	 * Model::UPDATE - сохраняет изменения модели в БД
	 * Model::DELETE - удаляет запись из БД
	 */
	public function save() {
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
	 * Функция возвращает подготовленную строку критериев выборки
	 *
	 * @return string
	 */
	protected static function _getCriteriaAsString($criteria) {
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
				return "$class::_alias." . $criteria [1] . " " . $criteria [0] . " " . $value;
			case 'is' :
				return "$class::_alias." . $criteria [1] . " " . $criteria [0] . " " . $criteria [2];
			case 'between' :
				$arguments = array_map ( function ($value) {
					if (gettype ( $value ) === 'string') {
						$value = "'" . $value . "'";
					}
					return $value;
				}, $criteria [2] );
				return "$class::_alias." . $criteria [1] . " " . $criteria [0] . " " . join ( " and ", $arguments );
			case 'in' :
			case 'not in' :
				$arguments = array_map ( function ($value) {
					if (gettype ( $value ) === 'string') {
						$value = "'" . $value . "'";
					}
					return $value;
				}, $criteria [2] );
				return "$class::_alias." . $criteria [1] . " " . $criteria [0] . " (" . join ( ',', $arguments ) . ")";
			default :
				throw \Exception ( "Ошибка в формате критериев. Недопустимый оператор: $operator" );
		}
	}
}