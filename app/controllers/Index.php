<?php
namespace app\controllers;

use core\Framework;
class Index extends \core\Controller {

	protected function _index($args = array()){
		$store = new \app\stores\TestStore();
		$store->load();
		$store->add(array(
			'login' => 'user1',
			'password' => 'password1'
		));
		$store->add(array(
				'login'=>'user2',
				'password'=>'password2'
		));
		$store->save();
	}

}
