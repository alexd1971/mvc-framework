<?php

namespace app\views;

use \core\MVCF;

/**
 *
 * @author Алексей Данилевский
 *
 */
class LoginForm extends \core\View {

	public function __construct() {

		parent::__construct();

		$app = MVCF::app ();
		$app->registerAssets ( array (
				"function_password" => array (
						"type" => "javascript",
						"text" => <<<SCRIPT
function showPassword() {

    var key_attr = $('#password').attr('type');

    if(key_attr != 'text') {

        $('.checkbox').addClass('show');
        $('#password').attr('type', 'text');

    } else {

        $('.checkbox').removeClass('show');
        $('#password').attr('type', 'password');

    }

}
SCRIPT

				)
		) );
		$app->addAssets ( array (
				'function_password'
		) );
	}
}