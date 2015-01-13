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
			if (isset($request->email) && isset($request->key)){
				$app->user->name = $request->email;
				$app->user->authenticated = true;
				$app->user->storeInSession();
				$app->redirect($app->createURL(''));
			}
			else {
				$loginForm = new \app\views\LoginForm;
				$app->title = "Войти в личный кабинет";
				$app->view->addData(array(
						"content" => $loginForm->render()
				));
			}
		}
	}

	protected function _logout() {

		session_destroy();
		$app = MVCF::app();
		$app->redirect($app->createURL(''));

	}
}