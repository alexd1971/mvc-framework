<?php

namespace app\controllers;

use \core\MVCF;
use \core\User;

/**
 * Контроллер аутентификации
 *
 * @author Алексей Данилевский
 *
 */
class Auth extends \core\Controller {

	protected function _login() {
		$app = MVCF::app();
		$request = $app->request;
		if ($app->user->authenticated) {
			$app->view->addData(array(
					"content" => "<h3>Вы уже вошли в систему</h3>"

			));
			$app->addMeta(array(
					"meta" => array(
							array(
									"http-equiv" => "Refresh",
									"content" => "3; url=" . $this->createURL('')
							)
					)
			));
		}
		else {
			if (isset ($request->submit)){
				if (isset($request->login) && isset($request->password)){
					$app->user->name = $request->login;
					$app->user->authenticated = true;
					$app->user->storeInSession();
					$referer = isset ($_SESSION['http_referer'])?($_SESSION['http_referer']):($this->createURL(''));
					$app->redirect($referer);
				}
			}
			else {
				if (isset ($request->http_referer)){
					$_SESSION['http_referer'] = $request->http_referer;
				}
				$loginForm = new \app\views\LoginForm;
				$app->title = "Вход в систему";
				$app->view->addData(array(
						"content" => $loginForm->render()
				));
			}
		}
	}
}