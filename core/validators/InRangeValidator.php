<?php
namespace core\validators;

class InRangeValidator implements IValidator {
	
	public function check($params) {
		$min = $params['min'];
		$max = $params['max'];
		$value = $params['value'];
		$attribute = isset($params['attribute'])?$params['attribute']:"";
		if ($value || $value == 0) {
			$result = array();
			if ($value >= $min && $value<=$max) {
				$result['valid'] = true;
			}
			else {
				$result['valid'] = false;
				$result['message'] = isset($params['message'])?$params['message']:"Значение поля \"$attribute\" должно быть в промежутке от $min до $max";
			}
		}
		else {
			$result ["valid"] = true;
		}
		return $result;
	}
	
}