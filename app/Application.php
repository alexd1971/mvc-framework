<?php

namespace app;
/**
 * Web-приложение
 *
 * @author Aleksey.Danilevskiy
 *
 */
class Application extends \core\Application {

	public function run(){
		session_start();
		parent::run();
	}

}
