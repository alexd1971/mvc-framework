<?php

namespace app\controllers;

use core\MVCF;

class Index extends \core\Controller {

	protected function _index($args = array()) {
		MVCF::app()->addContent(array(
			"content" => "<h3>Hello World from my Application!!!</h3>",
		));
	}
}
