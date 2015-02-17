<?php
namespace core\validators;

class RequiredValidator implements IValidator {

	public function check($params) {
		$value = $params ['value'];
		$result = array ();
		if ($value !== '') {
			$result['valid'] = true;
		}
		else {
			$result["valid"] = false;
			$result["message"] = isset($params['message'])?$params['message']:"Поле является обязательным";
		}
		return $result;
	}

}