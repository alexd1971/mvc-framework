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
		$this->_loadAssets = array_merge($this->_loadAssets, $this->config['loadAssets']);
		$this->_meta = array_merge($this->_meta, $this->config['meta']);
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
		extract($this->_contents, EXTR_OVERWRITE);
		$customMetaTags = $this->generateCustomMetaTags();
		require $this->_appBaseDir . '/' . $this->config['templates'] . '/' . $this->_template . '.php';

	}
	/**
	 * Функция устанавливает значения атрибутов класса.
	 * Список зарегистрированных атрибутов:
	 *
	 * Application::$defaultController
	 * Application::$template
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
			case 'template':
				if (gettype($value) === 'string'){
					$this->_template = $value;
				}
				else {
					throw \Exception ("Попытка установить недопустимое значение атрибута Application::".$attribute.". Требуется string, а не ".gettype($value));
				}
				break;
			case 'title':
				if (gettype($value) === 'string') {
					$this->addMeta(array("title" => $value));
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
			case 'title':
				$title = isset ($this->title) ? $this->title : '';
				return $title;
				break;
			default:
				throw \Exception ("Свойство $attribute не найдено");
		}
	}
	/**
	 * Функция проверяет наличие установленных значений атрибутов
	 * Список зарегистрированных атрибутов:
	 *
	 * Application::$defaultController
	 * Application::$appBaseDir
	 * Application::$template
	 * Application::$title
	 *
	 * @param unknown $attribute
	 * @return boolean
	 */
	public function __isset($attribute) {
		switch ($attribute) {
			case 'defaultController':
				return isset ($this->_defaultController);
				break;
			case 'appBaseDir':
				return isset ($this->_appBaseDir);
				break;
			case 'template':
				return isset ($this->_template);
				break;
			case 'title':
				return isset ($this->_meta['title']);
				break;
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
	 * Дополнения становятся доступными для загрузки. Однако, чтобы они были загружены, это необходимо
	 * явно указать с помощью функции Application::loadAssets($assets)
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
	public function loadAssets($list){
		if (is_array($list)) {
			$this->_loadAssets = array_merge($this->_loadAssets, $list);
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
	 * подключать функцией Application::loadAssets. Также для установки значения элемента title можно использовать атрибут MVCF::app()->title
	 *
	 * @param array $meta
	 */
	public function addMeta ($meta) {
		if (is_array($meta)) {
			$this->_meta = array_merge_recursive($this->_meta, $meta);
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
	protected $_loadAssets = array ();
	/**
	 * Дополнительная мета-информация для размещения в <head>
	 * Для добавления мета-элементов используется функция Application::addMeta($meta);
	 *
	 * @var array
	 */
	protected $_meta = array ();
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
		$loadAssets = array();
		foreach ($this->_loadAssets as $asset) {
			$assetConfig = $this->_registeredAssets[$asset];
			$loadAssets[] = $asset;
			if (isset ($assetConfig['depends'])){
				$loadAssets = array_merge($loadAssets, $assetConfig['depends']);
			}
		}
		$loadAssets = array_unique($loadAssets);
		foreach ($loadAssets as $asset) {
 			$assetConfig = $this->_registeredAssets[$asset];
			if ($assetConfig['type'] == 'javascript') {
				$metaTag = "<script type=\"text/javascript\" ";
				if (isset ($assetConfig['url'])) {
					$metaTag .= "src=\"" . $assetConfig['url'] . "\"></script>\n";
				}
				elseif (isset ($assetConfig['text'])) {
					$metaTag .= ">\n" . $assetConfig['text'] . "</script>\n";
				}
			}
 			if ($assetConfig['type'] == 'css') {
				if (isset ($assetConfig['url'])) {
					$metaTag = "<link rel=\"stylesheet\" href=\"" . $assetConfig['url']. "\">\n";
				}
				elseif (isset ($assetConfig['text'])) {
					$metaTag = "<style type=\"text/css\">\n" . $assetConfig['text'] . "</style>\n";
				}
 			}
 			$metaTags .= $metaTag;
 		}
		return $metaTags;
	}
}