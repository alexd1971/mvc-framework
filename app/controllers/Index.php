<?php

namespace app\controllers;

use core\MVCF;

class Index extends \core\Controller {

	protected function _index($args = array()) {
		$app = MVCF::app();
		$app->template = 'base';
		$app->addContent(array(
			"content" => "<h3>Hello World from base!!!</h3>",
		));
	}
}
