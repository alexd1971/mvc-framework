<?php

namespace core;

/**
 * Класс контроллер
 * Реализует базовый функционал котроллера web-приложения
 *
 * @author Алексей Данилевский
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
		$args = $args[0];
		$app = MVCF::app();
		$rule = $this->accessRule($action);
		if ($rule['access'] === "allow"){
			$function = "_$action";
			if (method_exists ( $this, $function )) {
				$this->$function ( $args );
			} else {
				// TODO: Вставить обработку 404 ошибки
				$app->view->addData(array(
						"content" => "Запрошенная страница не найдена"
				));
			}
		}
		elseif ($rule['access'] === "deny" && isset($rule['redirect'])){
				$app->redirect($app->createURL($rule['redirect']));
		}
		elseif($rule['access'] === "deny") {
			$app->view->addData(array(
					"content" => "Доступ к запрошенной странице запрещен"
			));
		}
	}
	/**
	 * Правила доступа к контроллеру и его действиям
	 *
	 * Правила обрабатываются последовательно
	 * Обработка правил прерывается при первом совпадении.
	 * Минимально необходимо для правила указать параметр "access", который может принимать значения "allow" или "deny"
	 * Если значение "access" => "deny", то можно определить дополнительный параметр "redirect", где можно указать путь к обработчику,
	 * выводящему информацию о запрете доступа. Если этот параметр не указан, то срабатывает обработчик по умолчанию.
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
	 * 			"access => "deny",
	 * 			"redirect" => "path/to/handler"
	 * 		),
	 *
	 * 		array(
	 * 			"actions" => "action3",
	 * 			"custom" => "CheckerClassName"
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
	protected $accessRules = array ();
	/**
	 * Функция проверяет доступ к действиям контроллера
	 * Возвращает правило доступа к указанному действию контроллера.
	 *
	 * @param string $action
	 * @return array
	 */
	protected function accessRule($action){
		$access = array("access" => "deny");
		if ($this->accessRules == array()){
			$access['access'] = "allow";
		}
		else{
			foreach ($this->accessRules as $rule){
				$match = true;
				foreach ($rule as $criteria => $value){
					switch ($criteria) {
						case "actions":
							if(!(is_array($value)?in_array($action, $value):($value=="*")?true:false)) {
								$match = false;
								break 2;
							}
							break;
						case "users":
							$name = MVCF::app()->user->name;
							if(!(is_array($value)?in_array($name, $value):($value=="*")?true:false)) {
								$match = false;
								break 2;
							}
							break;
						case "roles":
							$roles = MVCF::app()->user->roles;
							if(!(is_array($value)?(array_intersect($roles, $value)!== array()):($value=="*")?true:false)) {
								$match = false;
								break 2;
							}
							break;
						case "custom":
							$customChecker = new $value;
							if (!$customChecker->check()){
								$match = false;
								break 2;
							}
							break;
					}
				}
				if ($match) {
					$access = $rule;
					break;
				}
			}
		}
		return $access;
	}
	/**
	 * Действие по умолчанию для контроллера
	 *
	 * @param array $args
	 */
	protected function _index($args = array()) {
		echo "Hello World!!!";
	}
}