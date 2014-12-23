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
	 * Устанавливает шаблон представления. Поиск шаблона осуществляется в каталоге, указанном в конфигурационном файле приложения
	 *
	 * @param string $template //Имя представления
	 */
	public function __construct($template = 'default'){

		$templatePath = MVCF::application()->config['templates'].'/'.$template.'.php';
		if (file_exists($templatePath)){
			$this->template = $template;
		}
		else {
			throw \Exception("Указан неверный путь к шаблону");
		}

	}
	/**
	 * Функция формирует готовое представление. Если $return == true, то функция возвращает сформированное представление в виде строки.
	 * Иначе - выводит представление на стандартное утройство вывода.
	 * Шаблону доступна переменная $data, которая содержит либо данные модели, либо хранилище моделей (Store)
	 *
	 * @param Model or Store $data
	 * @param boolean $return
	 * @return string
	 */
	public function render($data = null, $return = true ) {

		if (is_a($data,'Model') or is_a($data,'Store')){
			if ($return) {
				ob_start();
				ob_implicit_flush(false);
				include $this->_template;
				$this->contents = ob_get_contents();
				return ob_get_clean();
			}
			else {
				include $this->_template;
			}
		}

	}
	/**
	 * Содержимое представления
	 *
	 * @var string
	 */
	protected $_contents = '';
	/**
	 * Путь к шаблону представления
	 *
	 *
	 * @var string
	 */
	protected $_template;

}