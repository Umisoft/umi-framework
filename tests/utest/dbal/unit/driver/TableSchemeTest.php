<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver;

use umi\dbal\driver\BaseTableScheme;
use umi\dbal\driver\IColumnScheme;
use utest\dbal\DbalTestCase;

/**
 * Тестирование работы со схемой таблицы
 *
 */
class TableSchemeTest extends DbalTestCase
{

    protected function setUpFixtures()
    {

        $driver = $this->getDbServer()
            ->getDbDriver();

        $driver->dropTable('test_table1');
        $driver->dropTable('test_table2');

        $table1 = $driver->addTable('test_table1');
        $table2 = $driver->addTable('test_table2');

        $table1->addColumn('test1', IColumnScheme::TYPE_INT);
        $table1->addColumn('test2', IColumnScheme::TYPE_VARCHAR);

        $table2->addColumn('test1', IColumnScheme::TYPE_SERIAL);
        $table2->addColumn('test2', IColumnScheme::TYPE_INT);
        $table2->addColumn('test3', IColumnScheme::TYPE_VARCHAR);

        $table2->setPrimaryKey('test1');
        $table2->addIndex('test2')
            ->addColumn('test2');
        $table2->addIndex('test3')
            ->addColumn('test3');

        $table2->addConstraint('fk0', 'test3', 'test_table1', 'test1');
        $table2->addConstraint('fk1', 'test2', 'test_table1', 'test2');

        $driver->applyMigrations();
    }

    protected function tearDownFixtures()
    {
        $driver = $this->getDbServer()
            ->getDbDriver();
        $driver->dropTable('test_table1');
        $driver->dropTable('test_table2');
    }

    public function testTable()
    {
        $table = $this->getDbServer()
            ->getDbDriver()
            ->getTable('test_table2');

        $this->assertEquals('test_table2', $table->getName());

        $this->assertFalse($table->getIsNew());
        $this->assertFalse($table->getIsModified());
        $this->assertFalse($table->getIsDeleted());

        $this->assertEquals([], $table->getMigrationQueries());

        $this->assertInstanceOf('umi\dbal\driver\ITableScheme', $table->setIsDeleted());

        $this->assertNotEmpty($table->getMigrationQueries());

        $this->assertInstanceOf('umi\dbal\driver\ITableScheme', $table->setIsNew());
        $this->assertInstanceOf('umi\dbal\driver\ITableScheme', $table->setIsModified());

        $this->assertTrue($table->getIsNew());
        $this->assertTrue($table->getIsModified());
        $this->assertTrue($table->getIsDeleted());
    }

    public function testTableColumns()
    {
        /**
         * @var BaseTableScheme $table
         */
        $table = $this->getDbServer()
            ->getDbDriver()
            ->getTable('test_table2');

        $this->assertTrue($table->getColumnExists('test1'));
        $this->assertFalse($table->getColumnExists('missed_column'));

        $columns = $table->getColumns();
        $this->assertCount(3, $columns);
        $this->assertInstanceOf('umi\dbal\driver\IColumnScheme', $columns['test1']);

        $this->assertFalse($table->getIsModified(), 'Ожидается, что загрузка колонок не модифицирует таблицу');

        $column = $table->getColumn('test1');
        $this->assertInstanceOf('umi\dbal\driver\IColumnScheme', $column);
        $this->assertSame($columns['test1'], $column);

        $e = null;
        try {
            $table->getColumn('missed_column');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\NonexistentEntityException', $e);

        $table->getColumn('test3')
            ->setType(IColumnScheme::TYPE_INT);
        $this->assertTrue($table->getIsModified(), 'Ожидается, что при модификации колонки изменяется и таблица');
        $table->setIsModified(false);

        $this->assertInstanceOf(
            'umi\dbal\driver\IColumnScheme',
            $table->addColumn('test4', IColumnScheme::TYPE_VARCHAR)
        );
        $this->assertTrue($table->getIsModified(), 'Ожидается, что при добавлении колонки изменяется и таблица');
        $table->setIsModified(false);

        $this->assertInstanceOf('umi\dbal\driver\ITableScheme', $table->deleteColumn('test2'));
        $this->assertTrue($table->getIsModified(), 'Ожидается, что при удалении колонки изменяется и таблица');
        $table->setIsModified(false);

        $this->assertEquals(['test3'], array_keys($table->getModifiedColumns()));
        $this->assertEquals(['test2'], array_keys($table->getDeletedColumns()));
        $this->assertEquals(['test4'], array_keys($table->getNewColumns()));
    }

    public function testTablePrimaryKey()
    {

        $table1 = $this->getDbServer()
            ->getDbDriver()
            ->getTable('test_table1');
        $this->assertNull($table1->getPrimaryKey());
        $e = null;
        try {
            $table1->deletePrimaryKey();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\NonexistentEntityException', $e);

        $table2 = $this->getDbServer()
            ->getDbDriver()
            ->getTable('test_table2');
        $this->assertInstanceOf('umi\dbal\driver\IIndexScheme', $table2->getPrimaryKey());
        $table2->deletePrimaryKey();
    }

    public function testTableIndexes()
    {
        /**
         * @var BaseTableScheme $table
         */
        $table = $this->getDbServer()
            ->getDbDriver()
            ->getTable('test_table2');

        $this->assertTrue($table->getIndexExists('test2'));
        $this->assertFalse($table->getIndexExists('missed_index'));

        $indexes = $table->getIndexes();
        $this->assertCount(2, $indexes);
        $this->assertInstanceOf('umi\dbal\driver\IIndexScheme', $indexes['test2']);

        $this->assertFalse($table->getIsModified(), 'Ожидается, что загрузка индексов не модифицирует таблицу');

        $index = $table->getIndex('test2');
        $this->assertInstanceOf('umi\dbal\driver\IIndexScheme', $index);
        $this->assertSame($indexes['test2'], $index);

        $e = null;
        try {
            $table->getIndex('missed_index');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\NonexistentEntityException', $e);

        $table->getIndex('test2')
            ->addColumn('test3');
        $this->assertTrue($table->getIsModified(), 'Ожидается, что при модификации индекса изменяется и таблица');
        $table->setIsModified(false);

        $this->assertInstanceOf(
            'umi\dbal\driver\IIndexScheme',
            $table->addIndex('test_index')
                ->addColumn('test2')
                ->addColumn('test3')
        );
        $this->assertTrue($table->getIsModified(), 'Ожидается, что при добавлении индекса изменяется и таблица');
        $table->setIsModified(false);

        $this->assertInstanceOf('umi\dbal\driver\ITableScheme', $table->deleteIndex('test3'));
        $this->assertTrue($table->getIsModified(), 'Ожидается, что при удалении индекса изменяется и таблица');
        $table->setIsModified(false);

        $this->assertEquals(['test2'], array_keys($table->getModifiedIndexes()));
        $this->assertEquals(['test3'], array_keys($table->getDeletedIndexes()));
        $this->assertEquals(['test_index'], array_keys($table->getNewIndexes()));
    }

    public function testTableConstraints()
    {
        /**
         * @var BaseTableScheme $table
         */
        $table = $this->getDbServer()
            ->getDbDriver()
            ->getTable('test_table2');

        $this->assertTrue($table->getConstraintExists('fk0'));
        $this->assertFalse($table->getConstraintExists('missed_constraint'));

        $constraints = $table->getConstraints();
        $this->assertCount(2, $constraints);
        $this->assertInstanceOf('umi\dbal\driver\IConstraintScheme', $constraints['fk0']);

        $this->assertFalse($table->getIsModified(), 'Ожидается, что загрузка внешних ключей не модифицирует таблицу');

        $constraint = $table->getConstraint('fk0');
        $this->assertInstanceOf('umi\dbal\driver\IConstraintScheme', $constraint);
        $this->assertSame($constraints['fk0'], $constraint);

        $e = null;
        try {
            $table->getConstraint('missed_constraint');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\NonexistentEntityException', $e);

        $table->getConstraint('fk0')
            ->setOnDeleteAction('CASCADE');
        $this->assertTrue(
            $table->getIsModified(),
            'Ожидается, что при модификации внешнего ключа изменяется и таблица'
        );
        $table->setIsModified(false);

        $this->assertInstanceOf(
            'umi\dbal\driver\IConstraintScheme',
            $table->addConstraint('FK_test3', 'test2', 'test_table1', 'test2')
        );
        $this->assertTrue($table->getIsModified(), 'Ожидается, что при добавлении внешнего ключа изменяется и таблица');
        $table->setIsModified(false);

        $this->assertInstanceOf('umi\dbal\driver\ITableScheme', $table->deleteConstraint('fk1'));
        $this->assertTrue($table->getIsModified(), 'Ожидается, что при удалении внешнего ключа изменяется и таблица');
        $table->setIsModified(false);

        $this->assertEquals(['fk0'], array_keys($table->getModifiedConstraints()));
        $this->assertEquals(['fk1'], array_keys($table->getDeletedConstraints()));
        $this->assertEquals(['FK_test3'], array_keys($table->getNewConstraints()));
    }
}

/**
 * Class WrongClass для тестирования
 */
class WrongClass
{

}
