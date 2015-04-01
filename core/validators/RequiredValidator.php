<?php
namespace core\validators;

class RequiredValidator implements IValidator {

	public function check($params) {
		$model = $params['model'];
		$attribute = $params['attribute'];
		$value = $model->$attribute;
		$result = true;
		if ($value === null || $value === "") {
			$result = false;
		}
		return $result;
	}

}