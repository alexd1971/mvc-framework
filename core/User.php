<?php

namespace core;

class User {
	/**
	 * Имя пользователя
	 *
	 * @var string
	 */
	public $name = "guest";
	/**
	 * Список ролей пользователя
	 *
	 * @var unknown
	 */
	public $roles = array();
	/**
	 * Профиль пользователя
	 *
	 * @var \core\Model
	 */
	public $profile;
	/**
	 * Признак аутентифицированного пользователя
	 *
	 * @var boolean
	 */
	public $authenticated = false;
	/**
	 * Адрес возврата для перенаправления пользователя.
	 * Здесь храниться URL, с которого был выполнен redirect
	 *
	 * @var string
	 */
	public $return_url = "";
	/**
	 * Функция сохраняет пользователя в переменной сессии
	 * Применеие:
	 *
	 * $user = new User;
	 * $user->name = "vasya";
	 * $user->storeInSession();
	 *
	 * ...
	 *
	 * $user = \core\User::loadFromSession();
	 *
	 */
	public function storeInSession () {

		$session = MVCF::app()->session;
		$session->user = serialize($this);
	}
	/**
	 * Функция загружает данные пользователя из переменных сессии
	 */
	public static function loadFromSession () {
		$session = MVCF::app()->session;
		if (isset($session->user)) {
			return unserialize($session->user);
		}
		else {
			return new get_class($this);
		}

	}
}