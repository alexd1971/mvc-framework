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
	 * Настройки приложения
	 * 
	 * @var array
	 */
	var $config = null;
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
		$this->config = include Framework::$index_base.'/'.Framework::$config['appNamespace'].'/config/config.php';
		$this->request = http\Request::getInstance();

	}
	/**
	 * Функция запуска приложения
	 */
	public function run(){
		/**
		 * Если в запросе указан контроллер, то используем его. Иначе - контроллер по умолчанию
		 */
		$appNamespace = Framework::$config['appNamespace'];
		$requestController = $appNamespace.'\\controllers\\' . ucfirst($this->request->controller !== ''?$this->request->controller:$this->defaultControllerName);
		if(class_exists($requestController)){
			$this->controller = new $requestController();
		}
		else{
//TODO: вставить обработку 404 ошибки
		}
		/**
		 * Если действие определено в запросе, то пытаемся выполнить его. Иначе - действие по умолчанию контроллера
		 */
		$requestAction = strtolower($this->request->action !== ''?$this->request->action:$this->controller->defaultAction);
		$this->controller->$requestAction($this->request->arguments);
	}

}