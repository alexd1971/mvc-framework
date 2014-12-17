<?php
namespace core\data;

use core\Framework;
class Store {
	/**
	 * Имя модели данных
	 *
	 * @var string
	 */
	var $model = '';
	/**
	 * Манипулятор для работы с БД
	 *
	 * @var
	 */
	var $dbConnection = null;
	/**
	 * Имя таблицы БД
	 *
	 * @var string
	 */
	var $table = '';
	/**
	 * Псевдоним таблицы БД
	 *
	 * @var string
	 */
	var $alias = '';
	/**
	 * var $criteria = array();
	 *
	 * Критерии выборки данных
	 * Формат определения критериев выборки соответствует предложению "where" sql-запроса.
	 *
	 * Одиночный критерий представляет собой массив, нулевой элемент которого представляет собой оператор, а остальные элементы - аргументы.
	 * Примеры критериев:
	 *
	 * 	array('=', "attribute1", $value1),
	 *  array('like', "attribute2", "%$val%"),
	 * 	array('between', "attribute3", array($val1, $val2)),
	 * 	array('in', "attribute4", array($val1, $val2, $val3, ...))
	 * 	array('is', "attribute5", 'not null')
	 *
	 * Для объединения критерии логическими связками "and" и "or":
	 *
	 * array("and", $criteria) // $criteria - массив критериев, которые нужно объединить связкой "and"
	 * array("or", $criteria) // $criteria - массив критериев, которые нужно объединить связкой "or"
	 *
	 * Для объединения критериев в примере выше логической связкой "and" получаем:
	 *
	 * array('and', array(
	 * 		array('=', "attribute1", $value1),
	 * 		array('like', "attribute2", "%$val%"),
	 * 		array('between', "attribute3", array($val1, $val2)),
	 * 		array('in', "attribute4", array($val1, $val2, $val3, ...)),
	 * 		array('is', "attribute5", 'not null'),
	 * 		array('or', array(
	 * 			array('=', "attribute6", $value6),
	 * 			array(...),
	 * 			...
	 * 		),
	 * ));
	 * @var array
	 */
	var $criteria = array();
	/**
	 * Размер страницы для загрузки данных. Значение 0 не ограничивает размер страницы.
	 *
	 * @var integer
	 */
	var $pageSize = 0;

	public function __construct(){

	}
	/**
	 * Функция загружает данные в хранилище в соответствии с установленными критериями
	 */
	public function load(){
		$dbh = Framework::application()->getDatabaseConnection($this->dbConnection);
		$model = $this->model;
		$attributes = $model::$attributes?"\n".join(",\n",$model::$attributes):'*';
		$criteria = $this->_getCriteriaAsString();
		$sql = <<<SQL
select $attributes
from $this->table $this->alias
SQL;
		$sql .= $criteria?"\nwhere $criteria":"";
		$res = $dbh->query($sql);
		print_r($res->fetchAll());
	}
	/**
	 * Функция сохраняет измененные данные в БД
	 */
	public function save(){

	}
	/**
	 * Функция возвращает подготовленную строку критериев выборки
	 *
	 * @return string
	 */
	protected function _getCriteriaAsString(){
		if ($this->criteria){
			return $this->_processCriteriaRecursive($this->criteria);
		}
		else {
			return '';
		}
	}
	/**
	 * Функция рекурсивно проходит массив критериев и возвращает строку критериев
	 *
	 * @param array $criteria
	 * @return string
	 */
	protected function _processCriteriaRecursive($criteria){
		$criteriaString = '';
		$operator = strtolower($criteria[0]);

		switch ($operator){
			case 'and':
			case 'or':
				$criteriaString = $this->_processCriteriaRecursive($criteria[1][0]);
				for ($i = 1; $i < count($criteria[1]); $i++){
					$criteriaString .= "\n$operator ".$this->_processCriteriaRecursive($criteria[1][$i]);
				}
				return "(\n".$criteriaString."\n)";
			case '=':
			case '>':
			case '<':
			case '!=':
			case '>=':
			case '<=':
			case 'like':
				$value = $criteria[2];
				if (gettype($value) === 'string'){
					$value = "'".$value."'";
				}
				return "$this->alias.".$criteria[1]." ".$criteria[0]." ".$value;
			case 'is':
				return "$this->alias.".$criteria[1]." ".$criteria[0]." ".$criteria[2];
			case 'between':
				$arguments = array_map(function($value){
					if (gettype($value) === 'string'){
						$value = "'".$value."'";
					}
					return $value;
				}, $criteria[2]);
				return "$this->alias.".$criteria[1]." ".$criteria[0]." ".join(" and ", $arguments);
			case 'in':
			case 'not in':
				$arguments = array_map(function($value){
					if (gettype($value) === 'string'){
						$value = "'".$value."'";
					}
					return $value;
				}, $criteria[2]);
				return "$this->alias.".$criteria[1]." ".$criteria[0]." (".join(',', $arguments).")";
		}
	}
	/**
	 * Хранилище данных
	 *
	 * @var array of Model
	 */
	protected $_data = array();
}