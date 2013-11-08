<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver;

/**
 * Интерфейс фабрики сущностей таблиц
 */
interface ITableFactory
{

    /**
     * Создает таблицу бд
     * @param $name
     * @param \umi\dbal\driver\IDbDriver $driver
     * @param IDbDriver $driver драйвер бд
     * @return ITableScheme
     */
    public function createTable($name, IDbDriver $driver);

    /**
     * Создает столбец в таблице бд
     * @param string $name имя столбца
     * @param string $internalType внутренний тип столбца
     * @param array $options список параметров столбца
     * @param IDbDriver $driver драйвер БД
     * @param ITableScheme $table таблица
     * @return IColumnScheme
     */
    public function createColumn($name, $internalType, $options, IDbDriver $driver, ITableScheme $table);

    /**
     * Создает индекс в таблице
     * @param string $name имя индекса
     * @param ITableScheme $table таблица
     * @return IIndexScheme
     */
    public function createIndex($name, ITableScheme $table);

    /**
     * Создает внешний ключ в таблице
     * @param string $name имя внешнего ключа
     * @param ITableScheme $table таблица
     * @return IConstraintScheme
     */
    public function createConstraint($name, ITableScheme $table);
}
