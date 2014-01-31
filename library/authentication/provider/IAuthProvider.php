<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\provider;

/**
 * Интерфейс провайдера аутентификации.
 */
interface IAuthProvider
{
    /**
     * Возвращает данные авторизации для адаптера
     * @return array|bool [имя пользователя, пароль], либо false если данные не существуют
     */
    public function getCredentials();
}