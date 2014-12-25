<?php

namespace core;

/**
 * Класс контроллер
 * Реализует базовый функционал котроллера web-приложения
 *
 * @author Aleksey.Danilevskiy
 *
 */
class Controller {
	/**
	 * Действие по умолчанию
	 *
	 * @var string
	 */
	public $defaultAction = 'index';
	/**
	 * Правила доступа к контроллеру и его действиям
	 * Общий вид описания правил:
	 *
	 * array(
	 * 		"action" => array(
	 * 			"authenticated" => array (array(),"allow"),
	 * 			"roles" => (array("role1", "role2"), "allow"),
	 * 			"function" => (array("arg1" => arg1, "arg2" => arg2), "deny"),
	 * 			"isGuest" => (array(), "deny"),
	 * 		)
	 * );
	 *
	 * @var array
	 */
	public $access = array ();
	/**
	 * Все действия контроллера размещаются в области видимости protected с префиксом "_".
	 * Например, для действия index метод,
	 * выполняющий действие будет называться _index.
	 * Такой механизм позволяет перед выполнением действия автоматизировать проверку прав на выполнение действия, а также
	 * автоматизировать вызов пользовательских функций beforeAction и afterAction, где "Action" наименование вызываемого действия
	 *
	 * @param string $action
	 * @param array $args
	 */
	public function __call($action, $args) {
		$function = "_$action";
		if (method_exists ( $this, $function )) {
			$this->$function ( $args );
		} else {
			// TODO: Вставить обработку 404 ошибки
		}
	}
	/**
	 * Действие по умолчанию для контроллера
	 *
	 * @param unknown $args
	 */
	protected function _index($args = array()) {
		echo "Hello World!!!";
	}
	/**
	 * Функция формирует полный URL по относительному пути (относительно каталога с запускаемым файлом index.php
	 *
	 * @param string $path
	 * @return string
	 */
	protected function createURL ($path) {
		$request = MVCF::app()->request;
		return $request->request_scheme . '://' . $request->http_host . '/' . MVCF::$indexDir . '/' . $path;
	}
}