<?php

namespace app\models;

class TestModel extends \core\SqlDbModel {

	protected static $_dbConnection = 'db';
	protected static $_table = 'users';
	protected static $_attributes = array(
			"id",
			"login",
			"password"
	);
	protected static $_primaryKey = 'id';
}