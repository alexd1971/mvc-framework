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
		$model = $params['model'];
		$attribute = $params['attribute'];
		$value = $model->$attribute;
		$result = false;
		if(is_string($value)){
				$result = true;
		}

		return $result;
	}

}