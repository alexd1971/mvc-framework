<?php

namespace app\controllers;

use core\Framework;

class Index extends \core\Controller {
	protected function _index($args = array()) {
		$store = new \app\stores\TestStore ();
		$store->load ();
		$store->add ( array (
				'login' => 'user',
				'password' => 'pass'
		) );
		$store->save ();
		foreach ( $store->data as $model ) {
			print_r ( $model );
		}
	}
}
