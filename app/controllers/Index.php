<?php

namespace app\controllers;

use core\MVCF;

class Index extends \core\Controller {

	protected function _index($args = array()) {
		$app = MVCF::app();
		$app->title = "Заголовок страницы";
		$app->registerAssets(array(
				"style" => array (
						"type" => "css",
						"text" => <<<CSS
body {
	margin: 0;
}
CSS
				)
		));
		$app->loadAssets(array("style"));
		$app->view->addData(array(
			"content" => "<h3>Hello World from base!!!</h3>\n",
		));
	}
}
