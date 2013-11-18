<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\toolbox\factory;

use umi\dbal\driver\IDbDriver;
use umi\dbal\driver\ITableFactory;
use umi\dbal\driver\ITableScheme;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика таблиц и табличных сущностей.
 */
class TableFactory implements IFactory, ITableFactory
{
    use TFactory;

    /**
     * @var string $tableClass имя класса схемы таблицы
     */
    public $tableSchemeClass;
    /**
     * @var string $columnClass имя класса для столбца
     */
    public $columnSchemeClass;
    /**
     * @var string $indexClass имя класса для индекса
     */
    public $indexSchemeClass;
    /**
     * @var string $constraintClass имя класса для внешнего ключа
     */
    public $constraintSchemeClass;

    /**
     * {@inheritdoc}
     */
    public function createTable($name, IDbDriver $driver)
    {
        return $this->getPrototype(
                $this->tableSchemeClass,
                ['umi\dbal\driver\ITableScheme']
            )
            ->createInstance([$name, $driver, $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function createColumn($name, $internalType, $options, IDbDriver $driver, ITableScheme $table)
    {
        return $this->getPrototype(
                $this->columnSchemeClass,
                ['umi\dbal\driver\IColumnScheme']
            )
            ->createInstance([$name, $internalType, $options, $driver, $table]);
    }

    /**
     * {@inheritdoc}
     */
    public function createIndex($name, ITableScheme $table)
    {
        return $this->getPrototype(
                $this->indexSchemeClass,
                ['umi\dbal\driver\IIndexScheme']
            )
            ->createInstance([$name, $table]);
    }

    /**
     * {@inheritdoc}
     */
    public function createConstraint($name, ITableScheme $table)
    {
        return $this->getPrototype(
                $this->constraintSchemeClass,
                ['umi\dbal\driver\IConstraintScheme']
            )
            ->createInstance([$name, $table]);
    }
}
