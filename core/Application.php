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
class Application {
	/**
	 * Настройки приложения
	 *
	 * @var array
	 */
	public $config = null;
	/**
	 * HTTP-запрос клиента.
	 * Содержит переданные параметры и прочую полезную информацию
	 *
	 * @var http\Request
	 */
	public $request = null;
	/**
	 * Имя контроллера по умолчанию
	 *
	 * @var string
	 */
	public $defaultControllerName = 'Index';
	/**
	 * Активный контроллер приложения
	 *
	 * @var \core\Controller
	 */
	public $controller = null;
	public function __construct() {
		$this->config = include Framework::$index_base . '/' . Framework::$config ['appNamespace'] . '/config/config.php';
		$this->request = http\Request::getInstance ();
	}
	/**
	 * Функция запуска приложения
	 */
	public function run() {
		/**
		 * Если в запросе указан контроллер, то используем его.
		 * Иначе - контроллер по умолчанию
		 */
		$appNamespace = Framework::$config ['appNamespace'];
		$requestController = $appNamespace . '\\controllers\\' . ucfirst ( $this->request->controller !== '' ? $this->request->controller : $this->defaultControllerName );
		if (class_exists ( $requestController )) {
			$this->controller = new $requestController ();
		} else {
			// TODO: вставить обработку 404 ошибки
		}
		/**
		 * Если действие определено в запросе, то пытаемся выполнить его.
		 * Иначе - действие по умолчанию контроллера
		 */
		$requestAction = strtolower ( $this->request->action !== '' ? $this->request->action : $this->controller->defaultAction );
		$this->controller->$requestAction ( $this->request->arguments );
	}
	/**
	 * Функция возвращает PDO-подключение к БД.
	 * Подключение устанавливается в момент первого запроса.
	 *
	 * @param stringn $db
	 * @return PDO Object
	 */
	public function getDatabaseConnection($db) {
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
	 *
	 * @param array $config
	 */
	public function registerAssets($config) {
	}
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
	protected $_assets = array ();
}