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
}
