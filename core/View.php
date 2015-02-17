<?php
namespace core;
/**
 * Класс View
 * Реализует базовое представление
 *
 * @author Алексей Данилевский
 *
 */
class View {
	/**
	 * Конструктор
	 */
	public function __construct() {

		$app = MVCF::app();
		$rc = new \ReflectionClass($this);
		$this->_template = (($app->module)?($app->module) . '/':'') . 'templates/' . strtolower($rc->getShortName()) . '.php';

	}
	/**
	 * Функция формирует готовое представление.
	 *
	 * @param array $data
	 * @param boolean $return // Если true, то возвращает результат в виде строки.
	 * @return string
	 */
	public function render($return = true) {
		$app = MVCF::app();
		$template = $_SERVER['DOCUMENT_ROOT'] . '/' . $app->baseDir . '/' . $this->_template;
		extract($this->_data, EXTR_OVERWRITE);

		if ($return) {
			ob_start();
			ob_implicit_flush(false);
			include $template;
			return ob_get_clean();
		}
		else {
			include $template;
		}

	}
	/**
	 * Функция добаляет параметры к параметрам представления
	 * Параметр $data является ассоциативным массивом, ключи которого доступны в шаблоне представления
	 * в виде одноименных переменных
	 *
	 * @param array $data
	 */
	public function addData($data) {
		if(array_values($data) !== $data) {
			$this->_data = array_merge($this->_data, $data);
		}
	}
	/**
	 * Устанавливает значение атрибута
	 *
	 * @param string $attribute
	 * @param unknown $value
	 */
	public function __set ($attribute, $value) {
		switch ($attribute) {
			case 'template':
				if (gettype($value) === 'string'){
					$this->_template = $value;
				}
				break;
			default:
				throw new \Exception("Атрибут $attribute не найден");
		}
	}
	/**
	 * Получает значение атрибута
	 *
	 * @param string $attribute
	 * @return unknown
	 */
	public function __get ($attribute) {
		switch ($attribute) {
			case 'template':
				return $this->_template;
				break;
			default:
				throw new \Exception("Атрибут $attribute не найден");
		}
	}
	/**
	 * Проверяет установлено ли значение для атрибута
	 *
	 * @param string $attribute
	 * @return boolean
	 */
	public function __isset ($attribute) {
		switch ($attribute) {
			case 'template':
				return isset($this->_template);
				break;
			default:
				return false;
		}
	}

	/**
	 * Шаблон представления.
	 * По умолчанию устанавливается в соответствии с именем класса представления
	 *
	 * @var string
	 */
	protected $_template;
	/**
	 * Параметры представления.
	 * Заполняются с помощью функции View::addData($data);
	 *
	 * @var array
	 */
	protected $_data = array();
}