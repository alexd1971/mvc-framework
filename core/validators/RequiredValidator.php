<?php
namespace core\validators;

class RequiredValidator implements IValidator {

	public function check($params) {
		$model = $params['model'];
		$attribute = $params['attribute'];
		$value = $model->$attribute;
		$result = false;
		if ($value !== '') {
			$result = true;
		}
		return $result;
	}

}