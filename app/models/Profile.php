<?php
namespace app\models;

class Profile extends \core\SqlDBModel {

	protected static	$_dbConnection = 'db';
	protected static 	$_table = 'profiles';
	protected static	$_alias ='p';
	protected  static	$_attributes = array(
			"id_user",
			"attribute",
			"value"
	);
}