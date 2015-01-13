<?php

namespace core;

interface IAccessChecker {
	/**
	 * Функция выполняет проверку доступа к действию/действиям контроллера/контрллеров.
	 *
	 * @param array $params
	 * @return boolean
	 */
	public function check ($params);
}