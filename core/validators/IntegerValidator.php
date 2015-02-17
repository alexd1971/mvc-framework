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
		$value = $params ['value'];
		$attribute = isset($params['attribute'])?$params['attribute']:"";
		if($value || $value === 0){
			$result = array ();
			if (is_numeric ( $value ) && gettype ( $value + 0 ) == "integer") {
				$result ["valid"] = true;
			} else {
				$result ["valid"] = false;
				$result ["message"] = "Значение поля \"$attribute\" должно быть целочисленным";
			}
		}
		else {
			$result ["valid"] = true;
		}
		return $result;
	}
}