<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication;

use umi\authentication\provider\IAuthProvider;
use umi\authentication\result\IAuthResult;
use umi\authentication\storage\IAuthStorage;

/**
 * Интерфейс менеджера аутентификации.
 */
interface IAuthentication
{
    /**
     * @return IAuthStorage
     */
    public function getStorage();

    /**
     * Производит авторизацию.
     * @param IAuthProvider $provider провайдер авторизации
     * @return IAuthResult
     */
    public function authenticate(IAuthProvider $provider);

    /**
     * Возвращает статус авторизации.
     * @return bool
     */
    public function isAuthenticated();

    /**
     * Стирает авторизационные данные.
     * @return $this
     */
    public function forget();
}
