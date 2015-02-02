<?php

namespace core;

/**
 * Класс Application
 *
 * Предоставляет базовый функционал работы web-приложения.
 *
 * @author Алексей Данилевский
 *
 */
class Application {
	/**
	 * Конструктор
	 */
	public function __construct() {

		$this->_baseDir = (MVCF::$indexDir?MVCF::$indexDir . '/':'') . MVCF::$config ['appNamespace'];

		$this->_config = include $_SERVER['DOCUMENT_ROOT'] . '/' . $this->_baseDir . '/config/config.php';

		$this->_registeredAssets = $this->config['assets'];

		$this->_addAssets = array_merge($this->_addAssets, $this->config['addAssets']);

		$this->_meta = array_merge($this->_meta, $this->config['meta']);

		$this->_defaultController = $this->config['defaultController'];

		$this->_request = Request::getInstance ();
		if(isset($this->request->params)){
			if (isset($this->request->params[0])) {
				if(in_array($this->request->params[0], array_keys($this->config['modules']))){
					$this->_module = $this->request->params[0];
					$defaultController = isset($this->config['modules'][$this->_module]['defaultController'])?$this->config['modules'][$this->_module]['defaultController']:'Index';
					$this->_controller = (isset($this->request->params[1]) && $this->request->params[1])?$this->request->params[1]:$defaultController;
					$this->_action = isset($this->request->params[2])?$this->request->params[2]:'';
					$this->_actionParams = array_slice ( $this->request->params, 3 );
				}
				else {
					$this->_controller = $this->request->params[0]?$this->request->params[0]:$this->_defaultController;
					$this->_action = isset($this->request->params[1])?$this->request->params[1]:'';
					$this->_actionParams = array_slice ( $this->request->params, 2 );
				}
			}
		}

		$this->_session = new Session();
	}
	/**
	 * Функция запуска приложения
	 */
	public function run() {
		$session = $this->session;

		if ($this->config['sessionAutoStart']) {
			$session->start();
		}

		if (isset ($session->user)) {
			$this->_user = User::loadFromSession();
		}
		else {
			$this->_user = new User;
			$this->_user->storeInSession();
		}

		$viewConfig = $this->config['view'];
		$viewClass = isset($viewConfig['class'])?$viewConfig['class']:'\core\View';
		$this->_view = new $viewClass;
		if (isset ($viewConfig['template'])) {
			$this->_view->template = $viewConfig['template'];
		}

		/**
		 * Если в запросе указан контроллер, то используем его.
		 * Иначе - контроллер по умолчанию
		 */
		$appNamespace = MVCF::$config ['appNamespace'];
		$controllerClass = $appNamespace . ($this->_module?'\\' . $this->_module:'') . '\\controllers\\' . ucfirst ( $this->_controller );
		if (class_exists ( $controllerClass )) {
			$controller = new $controllerClass ();
			$this->_action = strtolower($this->_action?$this->_action:$controller->defaultAction);
			$controller->{$this->_action}($this->_actionParams);
			$customMetaTags = $this->generateCustomMetaTags();
			$this->view->addData(array("customMetaTags" => $customMetaTags));
			$this->view->render(false);
		} else {
			$this->redirect('/errors/error_404');
		}
	}
	/**
	 * Функция устанавливает значения атрибутов класса.
	 * Список зарегистрированных атрибутов:
	 *
	 * Application::$defaultController
	 * Application::$view
	 * Application::$title
	 *
	 * @param string $attribute
	 * @param unknown $value
	 */
	public function __set($attribute, $value){
		switch ($attribute) {
			case 'defaultController':
				$this->_defaultController = $value;
				break;
			case 'view':
				if (gettype($value) === 'string'){
					$this->_view = new $value;
				}
				elseif (is_a($value, '\core\View')) {
					$this->_view = $value;
				}
				else {
					throw new \Exception ("Попытка установить недопустимое значение атрибута Application::".$attribute.". Требуется string, а не ".gettype($value));
				}
				break;
			case 'title':
				if (gettype($value) === 'string') {
					$this->_meta["title"] = $value;
				}
				else {
					throw new \Exception ("Попытка установить недопустимое значение атрибута Application::".$attribute.". Требуется string, а не ".gettype($value));
				}
				break;
			default:
				throw new \Exception ("Свойство $attribute не найдено");
		}
	}
	/**
	 * Функция возвращает значения атрибутов класса.
	 * Список зарегистрированных атрибутов:
	 *
	 * Application::$defaultController
	 * Application::$view
	 * Application::$baseDir
	 * Application::$user
	 *
	 * @param string $attribute
	 * @return string
	 */
	public function __get($attribute){
		switch ($attribute) {
			case 'defaultController':
				return $this->_defaultController;
			case 'view':
				return $this->_view;
			case 'baseDir':
				return $this->_baseDir;
			case 'title':
				$title = isset ($this->_meta['title']) ? $this->_meta['title'] : '';
				return $title;
			case 'user':
				return $this->_user;
			case 'config':
				return $this->_config;
			case 'request':
				return $this->_request;
			case 'session':
				return $this->_session;
			case 'module':
				return $this->_module;
			case 'controller':
				return $this->_controller;
			case 'action':
				return $this->_action;
			default:
				throw new \Exception ("Свойство $attribute не найдено");
		}
	}
	/**
	 * Функция проверяет наличие установленных значений атрибутов
	 * Список зарегистрированных атрибутов:
	 *
	 * Application::$defaultController
	 * Application::$baseDir
	 * Application::$view
	 * Application::$title
	 * Application::$user
	 *
	 * @param unknown $attribute
	 * @return boolean
	 */
	public function __isset($attribute) {
		switch ($attribute) {
			case 'defaultController':
				return isset ($this->_defaultController);
			case 'baseDir':
				return isset ($this->_baseDir);
			case 'view':
				return isset ($this->_view);
			case 'title':
				return isset ($this->_meta['title']);
			case 'user':
				return isset ($this->_user);
			case 'config':
				return isset ($this->_config);
			case 'request':
				return isset ($this->_request);
			case 'session':
				return isset ($this->_session);
			case 'module':
				return isset ($this->_module);
			case 'controller':
				return isset ($this->_controller);
			case 'action':
				return isset ($this->_action);
			default:
				return false;
		}
	}
	/**
	 * Функция возвращает PDO-подключение к БД.
	 * Подключение устанавливается в момент первого запроса.
	 *
	 * @param stringn $db
	 * @return PDO Object
	 */
// TODO: Переделать функцию в геттер атрибута
	public function getDBConnection($db) {
		if (array_key_exists ( $db, $this->_dbConnections )) {
			return $this->_dbConnections [$db];
		} elseif (array_key_exists ( $db, $this->config ['dbConnections'] )) {
			$dbConfig = $this->config ['dbConnections'] [$db];
			$dsn = $dbConfig ['driver'] . ":host=" . $dbConfig ['host'] . ";" . ($dbConfig ['port'] ? "port=" . $dbConfig ['port'] . ";" : "") . "dbname=" . $dbConfig ['dbname'] . ";user=" . $dbConfig ['user'] . ";password=" . $dbConfig ['password'];
			try {
				$dbh = new \PDO ( $dsn );
				if ($dbh) {
					$this->_dbConnections [$db] = $dbh;
					return $this->_dbConnections [$db];
				}
			} catch ( \PDOException $e ) {
				// TODO: Вставить обработку исключения
				echo $e->getMessage ();
			}
		} else {
			throw\Exception ( "Не найдена конфигурация для подключения $db" );
		}
	}
	/**
	 * Функция регистрирует дополнения для включение в приложение
	 * Дополнения становятся доступными для загрузки. Однако, чтобы они были загружены, это необходимо
	 * явно указать с помощью функции Application::addAssets($assets)
	 *
	 * @param array $config
	 */
	public function registerAssets($config) {
		if (is_array($config)) {
			$this->_registeredAssets = array_merge($this->_registeredAssets, $config);
		}
	}
	/**
	 * Функция добаляет список дополнений к загружаемым
	 *
	 * @param array $list
	 */
	public function addAssets($list){
		if (is_array($list)) {
			$this->_addAssets = array_merge($this->_addAssets, $list);
		}
	}
	/**
	 * Функция регистрирует в приложении мета-информацию, которую необходимо добавить в параметр customMetaTags при генерации представления.
	 * Этот параметр можно разместить в шаблоне представления в нутри элемента <head> для добавления сответствующей мета-информации на страницу
	 *
	 * Аргумент $meta является ассоциативным массивом вида:
	 *
	 * array (
	 * 		"title" => "Заголовок страницы",
	 *
	 * 		"meta" => array (
	 * 			"charset" => "utf-8",
	 * 			array (
	 *	 			"name" => "ProgId"
	 * 				"content" => "FrontPage.Editor.Document"
	 * 			)
	 * 		),
	 *
	 * 		"base" => array (
	 * 			"target" => "_blank"
	 * 		)
	 * )
	 *
	 * Здесь же можно размещать конфигурацию для включения дополнительных скриптов и css. Однако, удобнее использовать для этого работу с дополнениями.
	 * То есть дополнительные скрипты и css регистрировать в файле конфигурации приложения или налету с помощью функции Application::registerAssets, а затем
	 * подключать функцией Application::addAssets. Также для установки значения элемента title можно использовать атрибут MVCF::app()->title
	 *
	 * @param array $meta
	 */
	public function addMeta ($meta) {
		if (is_array($meta)) {
			$this->_meta = array_merge_recursive($this->_meta, $meta);
		}
	}
	/**
	 * Функция формирует полный URL по относительному пути (относительно каталога с запускаемым файлом index.php
	 *
	 * @param string $path
	 * @return string
	 */
	public function createURL ($path) {
		$url = "";
		if (preg_match('@^http://.*$@', $path) === 1){
			$url = $path;
		}
		else {
			$request = MVCF::app()->request;
			$url ='http://' . $request->http_host . '/' . (MVCF::$indexDir?MVCF::$indexDir . '/':'') . ltrim($path, "/");
		}
		return $url;
	}
	/**
	 * Функция перенаправляет клиента на указанный URL
	 *
	 * @param unknown $url
	 */
	public function redirect($url) {
		$request = MVCF::app()->request;
		$user = MVCF::app()->user;
		$return_url = 'http://' . $request->http_host . $request->request_uri;
		$user->return_url = $return_url;
		$user->storeInSession();
		header("Location: $url");
		exit();

	}
	/**
	 * Настройки приложения
	 *
	 * @var array
	 */
	protected  $_config = null;
	/**
	 * Запрос клиента.
	 * Содержит переданные параметры и прочую полезную информацию
	 *
	 * @var core\Request
	 */
	protected  $_request = null;
	/**
	 * Активная сессия
	 *
	 * @var \core\Session
	 */
	protected  $_session = null;
	/**
	 * Запрошенный модуль
	 *
	 * @var string
	 */
	protected $_module = "";
	/**
	 * Активный контроллер приложения
	 *
	 * @var string
	 */
	protected  $_controller = "";
	/**
	 * Запрошенное действие
	 *
	 * @var string
	 */
	protected $_action = "";
	/**
	 * Дополнительные параметры для выполнения действия
	 *
	 * @var array
	 */
	protected $_actionParams = array();
	/**
	 * Корневой каталог приложения (Только чтение)
	 *
	 * @var string
	 */
	protected $_baseDir = '';
	/**
	 * Имя контроллера по умолчанию
	 *
	 * @var string
	 */
	protected $_defaultController = 'Index';
	/**
	 * Массив подключений к БД
	 *
	 * @var array
	 */
	protected $_dbConnections = array ();
	/**
	 * Массив зарегистрированных в приложении дополнительных css- и js-файлов.
	 * На основе этих данных в шаблоне будут сгенерированы соответствующие html-элементы для загрузки этих файлов.
	 *
	 * @var array
	 */
	protected $_registeredAssets = array ();
	/**
	 * Список идентификаторов дополнений для загрузки
	 *
	 * @var array of string
	 */
	protected $_addAssets = array ();
	/**
	 * Дополнительная мета-информация для размещения в <head>
	 * Для добавления мета-элементов используется функция Application::addMeta($meta);
	 *
	 * @var array
	 */
	protected $_meta = array ();
	/**
	 * Представление приложения.
	 *
	 * @var View
	 */
	protected $_view;
	/**
	 * Пользователь приложения
	 *
	 * @var \core\User
	 */
	protected $_user;
	/**
	 * Функция формирует строку с мета данными для включения в заголовок <head> страницы
	 *
	 * @return string
	 */
	protected function generateCustomMetaTags () {
		$metaTags = '';
		foreach ($this->_meta as $tag => $value) {
			switch ($tag) {
				case 'title':
					$metaTags .= "<title>$value</title>\n";
					break;
				case 'meta':
					if (is_array($value)) {
						foreach ($value as $key => $value1) {
							if (gettype($key) === 'string'){
								$metaTags .= "<meta $key=\"$value1\">\n";
							}
							elseif (is_array($value1)) {
								$metaTag = "<meta ";
								$attributes = array();
								foreach ($value1 as $attribute => $value2) {
									$attributes[] = "$attribute=\"$value2\"";
								}
								$metaTag .= implode(' ',$attributes) . ">";
								$metaTags .= "$metaTag\n";
							}
						}
					}
					break;
				case 'base':
					if (is_array($value)) {
						$metaTag = "<base ";
							$attributes = array();
							foreach ($value as $attribute => $value1) {
								$attributes[] = "$attribute=\"$value1\"";
							}
							$metaTag .= implode(' ',$attributes) . ">";
							$metaTags .= "$metaTag\n";
					}
					break;
				case 'link':
					//TODO: Добавить реализацию
					break;
				case 'script':
					//TODO: Добавить реализацию
					break;
			}
		}
		$addAssets = array();
		$addAssets = $this->getAssetsWithDependences($this->_addAssets);
		foreach ($addAssets as $asset) {
 			$assetConfig = $this->_registeredAssets[$asset];
			if ($assetConfig['type'] == 'javascript') {
				$metaTag = "<script type=\"text/javascript\" ";
				if (isset ($assetConfig['url'])) {
					$metaTag .= "src=\"/" . (MVCF::$indexDir?MVCF::$indexDir . "/":"") . $assetConfig['url'] . "\"></script>\n";
				}
				elseif (isset ($assetConfig['text'])) {
					$metaTag .= ">\n" . $assetConfig['text'] . "</script>\n";
				}
			}
 			if ($assetConfig['type'] == 'css') {
				if (isset ($assetConfig['url'])) {
					$metaTag = "<link rel=\"stylesheet\" href=\"/"  . (MVCF::$indexDir?MVCF::$indexDir . "/":"") . $assetConfig['url']. "\">\n";
				}
				elseif (isset ($assetConfig['text'])) {
					$metaTag = "<style type=\"text/css\">\n" . $assetConfig['text'] . "</style>\n";
				}
 			}
 			$metaTags .= $metaTag;
 		}
		return $metaTags;
	}

	protected function getAssetsWithDependences($assets){
		$dependencies = array();
		foreach($assets as $asset){
			$assetConfig = $this->_registeredAssets[$asset];
			if (isset($assetConfig['depends']) && $assetConfig['depends']){
				$dependencies = array_merge($dependencies, $this->getAssetsWithDependences($assetConfig['depends']));
			}
		}
		$dependencies = array_merge($dependencies, $assets);
		return array_unique($dependencies);
	}
}