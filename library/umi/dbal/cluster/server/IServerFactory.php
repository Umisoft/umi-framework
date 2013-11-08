<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\cluster\server;

use umi\dbal\driver\IDbDriver;

/**
 * Фабрика для создания серверов.
 */
interface IServerFactory
{
    /**
     * Создает и возвращает экземпляр сервера БД
     * @param string $serverId уникальный Id сервера
     * @param IDbDriver $driver драйвер БД
     * @param string $serverType тип сервера
     * @return IServer
     */
    public function create($serverId, IDbDriver $driver, $serverType = null);
}
