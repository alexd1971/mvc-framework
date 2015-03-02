<?php
namespace core\validators;
/**
 * Валидатор для проверки целочисленных значений
 *
 * @author Алексей Данилевский
 *
 */
class IntegerValidator implements IValidator {

	public function check($params) {
		$model = $params['model'];
		$attribute = $params['attribute'];
		$value = $model->$attribute;
		$result = true;
		if($value !== ""){
			$result = false;
			if (is_numeric ( $value ) && gettype ( $value + 0 ) == "integer") {
				$result = true;
			}
		}
		return $result;
	}
}