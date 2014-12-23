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
	 * Активный контроллер приложения
	 *
	 * @var \core\Controller
	 */
	public $controller = null;
	/**
	 * Конструктор
	 */
	public function __construct() {
		$this->_appBaseDir = MVCF::$indexDir . '/' . MVCF::$config ['appNamespace'];
		$this->config = include $this->_appBaseDir . '/config/config.php';
		$this->_registeredAssets = array_merge_recursive(MVCF::$config['assets'], $this->config['assets']);
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
		$appNamespace = MVCF::$config ['appNamespace'];
		$requestController = $appNamespace . '\\controllers\\' . ucfirst ( $this->request->controller !== '' ? $this->request->controller : $this->_defaultController );
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
		//TODO: Вставить добавление параметра customHeaders
		extract($this->_contents, EXTR_OVERWRITE);
		require $this->_appBaseDir . '/' . $this->config['templates'] . '/' . $this->_template . '.php';

	}
	/**
	 * Функция устанавливает значения атрибутов класса.
	 * Список зарегистрированных атрибутов:
	 *
	 * Application::$defaultController
	 * Application::$template
	 *
	 * @param string $attribute
	 * @param unknown $value
	 */
	public function __set($attribute, $value){
		switch ($attribute) {
			case 'defaultController':
				$this->_defaultController = $value;
				break;
			case 'template':
				if (gettype($value) === 'string'){
					$this->_template = $value;
				}
				else {
					throw \Exception ("Попытка установить недопустимое значение атрибута Application::".$attribute.". Требуется string, а не ".gettype($value));
				}
				break;
			default:
				throw \Exception ("Свойство $attribute не найдено");
		}
	}
	/**
	 * Функция возвращает значения атрибутов класса.
	 * Список зарегистрированных атрибутов:
	 *
	 * Application::$defaultController
	 * Application::$template
	 * Application::$appBaseDir
	 *
	 * @param string $attribute
	 * @return string
	 */
	public function __get($attribute){
		switch ($attribute) {
			case 'defaultController':
				return $this->_defaultController;
				break;
			case 'template':
				return $this->_template;
			case 'appBaseDir':
				return $this->_appBaseDir;
				break;
			default:
				throw \Exception ("Свойство $attribute не найдено");
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
		if (is_array($config)) {
			array_merge_recursive($this->_registeredAssets, $config);
		}
	}
	/**
	 * Функция добаляет список дополнений к загружаемым
	 *
	 * @param array $list
	 */
	public function loadAssets($list){
		if (is_array($list)) {
			array_merge($this->_loadedAssets, $list);
		}
	}
	/**
	 * Функция добавляет готовый фрагмент для вывода в результирующее представление.
	 * Параметр $content имеет вид:
	 *
	 * array("contentName" => contentValue);
	 *
	 * При генерации представления данный параметр будет преобразован в переменную с именеи $contentName и
	 * значением contentValue. Эта переменная будет доступна для использования в шаблоне представления.
	 *
	 * @param array $content
	 */
	public function addContent ($content){
		if (is_array($content)){
			$this->_contents = array_merge($this->_contents, $content);
		}
	}
	/**
	 * Корневой каталог приложения (Только чтение)
	 *
	 * @var string
	 */
	protected $_appBaseDir = '';
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
	protected $_loadedAssets = array ();
	/**
	 * <h3>Ассоциативный массив сгенерированных фрагментов результирующей страницы.</h3>
	 * 
	 * Массив заполняется посредством функции:
	 *
	 * Application::addContent(array("contentName" => contentValue));
	 *
	 * На этапе генерации страницы этот массив преобразуется в набор переменных и их значений вида:
	 *
	 * $contentName = contentValue; // contentValue может быть любого допустимого типа
	 *
	 * Все полученные переменные становятся доступны в шаблоне приложения
	 *
	 * @var array
	 */
	protected $_contents = array ();
	/**
	 * Имя шаблона приложения.
	 * Финальный шаблон, на основе которого генерируется представление приложения.
	 * Шаблон может использовать в качестве переменных параметры, сконфигурированные с помощью функции
	 *
	 * Application::addContent($content);
	 * @var string
	 */
	protected $_template = 'layout';
}