<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\adapter;

use umi\authentication\result\IAuthResult;

/**
 * Интерфейс адаптера аутентификации.
 */
interface IAuthAdapter
{
    /**
     * Производит авторизацию, используя имя пользователя и пароль
     * @param string $username имя пользователя
     * @param string $password пароль
     * @return IAuthResult результат авторизации
     */
    public function authenticate($username, $password);
}