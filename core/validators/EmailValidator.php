<?php
namespace core\validators;

class EmailValidator implements IValidator {

	public function check($params) {
		$value = $params['value'];
		if($value) {
			$email_match = preg_match("/^[-a-z0-9!#$%&'*+\/=?^_`{|}~]+(?:\.[-a-z0-9!#$%&'*+\/=?^_`{|}~]+)*@(?:[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])?\.)*(?:aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|[a-z][a-z])$/", $value);
			$result = array("valid" => false);
			if ($email_match !== false) {
				if ($email_match === 1) {
					$result["valid"] = true;
				}
				else {
					if( isset($params['message'])) {
						$result["message"] = $params['message'];
					}
					else {
						$result["message"] = "Некорректный email";
					}
				}
			}
			else {
				throw new \Exception("Фатальная ошибка при проверке email");
			}
		}
		else {
			$result['valid'] = true;
		}
		return $result;
	}

}