<?php
namespace core\validators;

class UniqueValidator implements IValidator {

	public function check($params) {

		$attribute = $params['attribute'];
		$value = $params ['value'];
		$model = $params['model'];
		$modelClass = get_class($model);

		$result = array ();
		if ($value || $value === 0) {
			$models = $modelClass::findByAttributes(array(
					$attribute => $value
			));
			if ($models){
				$result['valid'] = true;
				foreach ($models as $storedModel){
					if($model->id != $storedModel->id){
						$result['valid'] = false;
						$result["message"] = isset($params['message'])?$params['message']:"Значение должно быть уникальным";
						break;
					}
				}
			}
			else {
				$result["valid"] = true;
			}
		}
		else {
			$result["valid"] = true;
		}
		return $result;
	}

}