<?php
namespace app\stores;

class TestStore extends \core\data\Store {

	var $model = '\app\models\TestModel';

	var $dbConnection = 'db';

	var $table = 'users';

	var $alias = 'u';
	
	var $criteria = array("or", array(
			array("between", "id", array(2,9)),
			array("!=", 'login',"guest")
			
	));

}