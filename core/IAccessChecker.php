<?php

namespace core;

interface IAccessChecker {
	/**
	 * Функция выполняет проверку доступа к действию/действиям контроллера/контрллеров.
	 * Необходимые данные для проверки можно получить из атрибутов прилдожения:
	 * MVCF::app()->user - данные о текущем пользователе
	 * MVCF::app()->request->controller - запрошенный контроллер
	 * MVCF::app()->request->action - запрошенное действие
	 * MVCF::app()->request->arguments - параметры для действия
	 *
	 * @param array $params
	 * @return boolean
	 */
	public function check ();
}