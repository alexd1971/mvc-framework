<?php

namespace app\models;

class TestModel extends \core\DataBaseModel {

	protected static $_dbConnection = 'db';
	protected static $_table = 'users';
	protected static $_alias = 'u';
	protected static $_attributes = array(
			"id" => array(),
			"login" => array(),
			"password" => array()
	);
	protected static $_primaryKey = 'id';
	protected static $_pkSequence = "users_id_seq";
}