<?php
namespace core\validators;

/**
 * Валидатор для проверки строчных значений
 * Проверяет, является ли значение строкой.
 *
 * @author Алексей Данилевский
 *
 */
class StringValidator implements IValidator {

	public function check($params) {
		$value = $params['value'];
		if($value) {
			$result = array();
			if(is_string($value)){
				$result["valid"] = true;
			}
			else {
				$result["valid"] = false;
				$result["message"] = "Значение должно быть строкой";
			}
		}
		else {
			$result["valid"] = true;
		}

		return $result;
	}

}