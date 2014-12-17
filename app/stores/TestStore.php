<?php
namespace app\stores;

class TestStore extends \core\data\Store {

	var $model = '\app\models\TestModel';

	var $dbConnection = 'db';

	var $table = 'users';

	var $alias = 'u';

}