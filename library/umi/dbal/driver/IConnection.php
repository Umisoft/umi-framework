<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\PDOConnection;

/**
 * Соединение с БД, расширяющее функционал Doctrine\DBAL\Connection.
 */
interface IConnection extends Connection
{
    /**
     * Декорированное соединение с PDO драйвером
     * @return PDOConnection
     */
    public function getWrappedConnection();

    /**
     * Параметры, передаваемые драйверу PDO при создании
     * @return array
     */
    public function getParams();
}
