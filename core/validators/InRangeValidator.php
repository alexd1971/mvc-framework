<?php
namespace core\validators;

class InRangeValidator implements IValidator {

	public function check($params) {
		$model = $params['model'];
		$attribute = $params['attribute'];
		$min = $params['min'];
		$max = $params['max'];
		$value = $model->$attribute;
		$result = "";
		if ($value !== "") {
			$result = false;
			if (is_numeric($value) && $value >= $min && $value<=$max) {
				$result = true;
			}
		}
		return $result;
	}

}