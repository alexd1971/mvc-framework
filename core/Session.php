<?php
namespace core;

class Session {

	public function __get($attribute) {
		if (isset($this->$attribute)) {
			return $_SESSION[$attribute];
		}
		else {
			return null;
		}
	}

	public function __set ($attribute, $value) {
		$_SESSION[$attribute] = $value;
	}

	public function __isset($attribute) {
		if (isset($_SESSION[$attribute])){
			return true;
		}
		else {
			return false;
		}
	}

	public function __unset($attribute) {
		if (isset($_SESSION[$attribute])) {
			unset($_SESSION[$attribute]);
		}
	}

	public function start() {
		session_start();
	}

	public function destroy() {
		session_destroy();
	}
}