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
	 *
	 * Правила обрабатываются последовательно
	 * Обработка правил прерывается при первом совпадении.
	 * Минимально необходимо для правила указать параметр "access", который может принимать значения "allow" или "deny"
	 * Дополнительно для правила можно указывать список действий, список пользователей или список ролей, на которые распространяется правило.
	 * Возможен вариант пользовательской проверки "custom". В качестве параметров указывается либо имя класса,
	 * реализующего интерфейс IAccessChecker, либо имя функции контроллера, которая выполняет необходимую проверку, плюс список параметров,
	 * необходимых для проверки
	 *
	 * Если ни одно правило не подошло, то по умолчанию будет запрет доступа.
	 * Если ни одного правила не описано, то по умолчанию доступ к контроллеру будет открыт
	 *
	 * Общий вид описания правил:
	 *
	 * array(
	 * 		array(
	 * 			"actions" => array("action1", "action2", ...),
	 * 			"users" => array("user1", "user2", ... ),
	 * 			"roles" => array("role1", "role2", ...),
	 * 			"access" => "allow"
	 * 		),
	 *
	 * 		array(
	 * 			"actions" => "*",
	 * 			"users" => "user5",
	 * 			"access => "deny"
	 * 		),
	 *
	 * 		array(
	 * 			"actions" => "action3",
	 * 			"custom" => array("class" => "CheckerClassName", array("param1" => value1, ...))
	 * 		)
	 *
	 * 		array(
	 * 			"actions" => "*",
	 * 			"access" => "deny"
	 * 		)
	 * );
	 *
	 * @var array
	 */
	public $accessRules = array ();
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
		return 'http://' . $request->http_host . '/' . (MVCF::$indexDir?MVCF::$indexDir . '/':'') . $path;
	}
}