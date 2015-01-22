<?php

namespace app\controllers;

use \core\MVCF;

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
									"content" => "5; url=" . $app->createURL('')
							)
					)
			));
		}
		else {
			if (isset($request->email) && isset($request->key)){

				$userModel = \app\models\UserModel::findByAttributes(array(
						"login" => $request->email
				));
				print_r($userModel);
				exit();
				$app->user->name = $request->email;
				$app->user->authenticated = true;
				if (preg_match('/^admin.*$/', $app->user->name) === 1) {
					$app->user->roles[] = "admin";
				}
				$app->user->storeInSession();
				$app->redirect($app->user->return_url);
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
		$app = MVCF::app();
		$session = $app->session;
		$session->destroy();
		$app->redirect($app->createURL(''));

	}
}