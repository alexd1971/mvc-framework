<?php

namespace core;

/**
 * Класс Application
 *
 * Предоставляет базовый функционал работы web-приложения.
 * Реализует метод run() для запуска приложения
 *
 * @author Aleksey.Danilevskiy
 *
 */
class Application{
	/**
	 * HTTP-запрос клиента. Содержит переданные параметры и прочую полезную информацию
	 *
	 * @var http\Request
	 */
	var $request = null;
	/**
	 * Имя контроллера по умолчанию
	 *
	 * @var string
	 */
	var $defaultControllerName = 'Index';
	/**
	 * Активный контроллер приложения
	 *
	 * @var \core\Controller
	 */
	var $controller = null;

	public function __construct(){

		$this->request = http\Request::getInstance();

	}
	/**
	 * Функция запуска приложения
	 */
	public function run(){
		/**
		 * Если в запросе указан контроллер, то используем его. Иначе - контроллер по умолчанию
		 */
		$requestController = '\\app\\controllers\\' . ucfirst($this->request->controller !== ''?$this->request->controller:$this->defaultControllerName);
		$this->controller = new $requestController();
		/**
		 * Если действие определено в запросе, то пытаемся выполнить его. Иначе - действие по умолчанию контроллера
		 */
		$requestAction = strtolower($this->request->action !== ''?$this->request->action:$this->controller->defaultAction);
		$this->controller->$requestAction($this->request->arguments);
	}

}