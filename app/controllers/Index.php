<?php

namespace app\controllers;

use core\MVCF;

class Index extends \core\Controller {

	public function __construct(){
		$this->accessRules = array(
				array(
						"users" => array("guest"),
						"access" => "deny",
						"redirect" =>"auth/login"
				),
				array(
						"actions" => array("test"),
						"custom" => "\app\AccessChecker",
						"access" => "allow",
				),
				array(
						"access" => "allow"
				)
		);
	}

	protected function _index($args = array()) {
		$app = MVCF::app();
		$matrixView = new \app\views\Matrix;
		$app->title = "Матрица";
		$app->view->addData(array(
			"content" => $matrixView->render()
		));
	}

	protected function _test($args = array()) {
		$app = MVCF::app();
		$app->title = "test";
		$user = \app\models\TestModel::find(
				array("like", "login", "user%")
		);
		$app->view->addData(array(
				"content" => "Action test of Index controller.<br> Result: <pre>". print_r($user,true) . "</pre>"
		));
	}
}
