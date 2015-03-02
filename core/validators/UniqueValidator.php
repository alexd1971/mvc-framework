<?php
namespace core\validators;

class UniqueValidator implements IValidator {

	public function check($params) {
		$model = $params['model'];
		$attribute = $params['attribute'];
		$value = $model->$attribute;
		$modelClass = get_class($model);

		$result = false;
		if ($value !== "") {
			$models = $modelClass::findByAttributes(array(
					$attribute => $value
			));
			if ($models){
				$result = true;
				foreach ($models as $storedModel){
					if($model->id != $storedModel->id){
						$result = false;
						break;
					}
				}
			}
			else {
				$result = true;
			}
		}
		else {
			$result = true;
		}
		return $result;
	}

}