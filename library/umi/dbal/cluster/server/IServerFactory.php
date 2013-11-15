<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

    namespace umi\dbal\cluster\server;

    use Doctrine\DBAL\Connection;
    use umi\dbal\driver\IDialect;

    /**
     * Фабрика для создания серверов.
     */
    interface IServerFactory
    {
        /**
         * Создает и возвращает экземпляр сервера БД
         * @param string $serverId уникальный Id сервера
         * @param Connection $connection драйвер БД
         * @param IDialect $dialect
         * @param string $serverType тип сервера
         * @return IServer
         */
        public function create($serverId, Connection $connection, IDialect $dialect, $serverType = null);
    }
