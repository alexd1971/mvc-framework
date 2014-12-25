<?php

namespace app\controllers;

use core\MVCF;

class Index extends \core\Controller {

	protected function _index($args = array()) {
		session_destroy();
		$app = MVCF::app();
		if (!$app->user->authenticated) {
			$app->redirect($app->request->http_origin . '/' . MVCF::$indexDir . "/auth/login");
		}
		$app->title = "Заголовок страницы";
		$app->view->addData(array(
			"content" => print_r($app->user, true),
		));
	}
}
