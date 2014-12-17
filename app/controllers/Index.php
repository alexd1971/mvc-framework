<?php
namespace app\controllers;

use core\Framework;
class Index extends \core\Controller {

	protected function _index($args = array()){
		$store = new \app\stores\TestStore();
		$store->load();
	}

}
