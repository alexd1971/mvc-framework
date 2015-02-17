<?php
namespace core\validators;

class InRangeValidator implements IValidator {

	public function check($params) {
		$min = $params['min'];
		$max = $params['max'];
		$value = $params['value'];
		$attribute = isset($params['attribute'])?$params['attribute']:"";
		if ($value !== "") {
			$result = array();
			if (is_numeric($value) && $value >= $min && $value<=$max) {
				$result['valid'] = true;
			}
			else {
				$result['valid'] = false;
				$result['message'] = isset($params['message'])?$params['message']:"Значение поля \"$attribute\" должно быть числом в промежутке от $min до $max";
			}
		}
		else {
			$result ["valid"] = true;
		}
		return $result;
	}

}