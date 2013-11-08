<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver;

use umi\dbal\exception\NotAvailableDriverException;
use umi\dbal\exception\RuntimeException;

/**
 * Фабрика драйверов БД.
 */
interface IDbDriverFactory
{
    /**
     * Создает новый драйвер БД
     * @param string $type тип драйвера
     * @param array $options список опций драйвера
     * @throws RuntimeException если тип драйвера не известен
     * @throws NotAvailableDriverException если PDO-драйвер не доступен
     * @return IDbDriver
     */
    public function create($type, array $options = []);
}
