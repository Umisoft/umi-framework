<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver;

use umi\dbal\driver\IColumnScheme;
use umi\dbal\toolbox\factory\TableFactory;
use utest\dbal\DbalTestCase;

/**
 * Тестирование фабрики табличных сущностей
 */
class TableFactoryTest extends DbalTestCase
{
    public function testTableFactoryCreation()
    {
        $driver = $this->getDbServer()
            ->getDbDriver();

        $tableFactory = $this->instantiateTableFactory();
        $this->assertInstanceOf(
            'umi\dbal\driver\ITableFactory',
            $tableFactory,
            'Ожидается, что в драйвере бд скрыто создается фабрика табличных сущностей'
        );

        $tbl1 = $tableFactory->createTable('foo', $driver);
        $tbl2 = $driver->addTable('foo');
        $this->assertInstanceOf(
            'umi\dbal\driver\ITableScheme',
            $tbl1,
            'Ожидается, что ITableFactory::createTable() вернет ITableScheme'
        );

        //$this->assertTrue(get_class($tbl1) == get_class($tbl2), 'Ожидается, что драйвер не создает таблицы, а делегирует это фабрике');
        $this->assertTrue(
            $tbl1->getName() == $tbl2->getName(),
            'Ожидается, что драйвер не создает таблицы, а делегирует это фабрике'
        );

        $col1 = $tableFactory->createColumn('foo_col', IColumnScheme::TYPE_INT, [], $driver, $tbl1);
        $col2 = $tbl1->addColumn('foo_col', IColumnScheme::TYPE_INT);
        $this->assertInstanceOf(
            'umi\dbal\driver\IColumnScheme',
            $col1,
            'Ожидается, что ITableFactory::createColumn() вернет IColumnScheme'
        );
        //$this->assertTrue($col1->getName() == $col2->getName(), 'Ожидается, что драйвер не создает столбцы, а делегирует это фабрике');
        //$this->assertTrue(get_class($col1) == get_class($col2), 'Ожидается, что драйвер не создает столбцы, а делегирует это фабрике');
    }

    public function testIndices()
    {
        $driver = $this->getDbServer()
            ->getDbDriver();
        $tableFactory = $this->instantiateTableFactory();
        $tbl1 = $driver->addTable('test_users');
        $ind1 = $tableFactory->createIndex('foo_ind', $tbl1);
        $ind2 = $tbl1->addIndex('foo_ind');
        $this->assertInstanceOf(
            'umi\dbal\driver\IIndexScheme',
            $ind1,
            'Ожидается, что ITableFactory::createIndex() вернет IIndexScheme'
        );
        //$this->assertTrue($ind1->getName() == $ind2->getName(), 'Ожидается, что драйвер не создает индексы, а делегирует это фабрике');
        //$this->assertTrue(get_class($ind1) == get_class($ind2), 'Ожидается, что драйвер не создает индексы, а делегирует это фабрике');
    }

    public function testConstraints()
    {
        $tableFactory = $this->instantiateTableFactory();
        $driver = $this->getDbServer()
            ->getDbDriver();

        $tbl1 = $driver->addTable('test_users');
        $driver->getTable('test_users');

        $foreignTable = $driver->addTable('foreign', $tableFactory);
        $foreignTable->addColumn('bar', IColumnScheme::TYPE_INT);

        $fk1 = $tableFactory->createConstraint('foo_fk', $tbl1);
        $fk2 = $tbl1->addConstraint('foo_fk', 'foo_col', $foreignTable, 'bar');
        $this->assertInstanceOf(
            'umi\dbal\driver\IConstraintScheme',
            $fk1,
            'Ожидается, что ITableFactory::createConstraint() вернет IConstraintScheme'
        );
        //$this->assertTrue($fk1->getName() == $fk2->getName(), 'Ожидается, что драйвер не создает внешние ключи, а делегирует это фабрике');
        //$this->assertTrue(get_class($fk1) == get_class($fk2), 'Ожидается, что драйвер не создает внешние ключи, а делегирует это фабрике');
    }

    /**
     * @return TableFactory
     */
    private function instantiateTableFactory()
    {
        $tableFactory = new TableFactory();
        $this->resolveOptionalDependencies($tableFactory);

        $tableFactory->columnSchemeClass = 'umi\dbal\driver\ColumnScheme';
        $tableFactory->constraintSchemeClass = 'umi\dbal\driver\ConstraintScheme';
        $tableFactory->tableSchemeClass = 'umi\dbal\driver\mysql\MySqlTable';
        $tableFactory->indexSchemeClass = 'umi\dbal\driver\IndexScheme';

        return $tableFactory;
    }

}
