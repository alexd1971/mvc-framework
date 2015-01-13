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

		$_SESSION['user'] = serialize($this);
	}
	/**
	 * Функция загружает данные пользователя из переменных сессии
	 */
	public static function loadFromSession () {

		if (isset($_SESSION['user'])) {
			return unserialize($_SESSION['user']);
		}
		else {
			return new get_class($this);
		}

	}
}