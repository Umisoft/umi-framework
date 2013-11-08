<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\result;

use umi\authentication\adapter\IAuthAdapter;

/**
 * Фабрика результатов аутентификации.
 * @internal
 */
interface IAuthenticationResultFactory
{
    /**
     * Возвращает объект результата авторизации.
     * @param int $status статус авторизации
     * @param mixed|null $identity ресурс, полученый в результате авторизации
     * @return IAuthAdapter
     */
    public function createResult($status, $identity);
}