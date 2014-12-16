<?php
namespace core\data;

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
	 * Критерии выборки данных
	 * 
	 * @var array
	 */
	var $criteria = array();
	
	public function __construct(){
		
	}
}