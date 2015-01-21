<?php
namespace core\validators;

interface IValidator {
	/**
	 * Функция выполняет проверку допустимости значения, которое передается в $params["value"] и,
	 * в случае допустимого значения возвращает:
	 *
	 * array("valid" => true)
	 *
	 * В случае недопустимого значения возвращает:
	 *
	 * array(
	 * 		"valid" => false,
	 * 		"message" => "Сообщение об ошибке"
	 * )
	 *
	 * Параметр message возвращается либо $params['message'] (если определено явно, какое сообщение нужно получить),
	 * либо сообщение по умолчанию.
	 *
	 * @param array $params
	 * @return array
	 */
	public function check($params);
}