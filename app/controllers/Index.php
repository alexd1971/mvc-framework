<?php

namespace app\controllers;

use core\MVCF;

class Index extends \core\Controller {

	protected function _index($args = array()) {
		$app = MVCF::app();
		if (!$app->user->authenticated) {
			$app->redirect($app->request->http_origin . '/' . MVCF::$indexDir . "/auth/login");
		}
		$matrixView = new \app\views\Matrix;
		$app->title = "Матрица";
		$app->view->addData(array(
			"content" => $matrixView->render()
		));
	}
}
