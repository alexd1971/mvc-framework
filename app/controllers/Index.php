<?php

namespace app\controllers;

use core\MVCF;

class Index extends \core\Controller {

	public function __construct(){

		$this->accessRules = array(

				array(
						"access" => "allow"
				)
		);
	}

	protected function _index($args = array()) {
		$app = MVCF::app();
		$indexView = new \app\views\Index;
		$indexView->addData(array(
				"message" => "Это сообщение передано в шаблон из контроллера!"
		));
		$app->title = "MVCF";
		$app->view->addData(array(
			"content" => $indexView->render()
		));
	}

}
