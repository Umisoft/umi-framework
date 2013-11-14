<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\sqlite;

use utest\dbal\DbalTestCase;

/**
 * Тестирование индексов sqlite драйвера
 */
class SqliteIndexesTest extends DbalTestCase
{

    protected function setUpFixtures()
    {

        $driver = $this->getSqliteServer()
            ->getDbDriver();
        $driver->modify('DROP TABLE IF EXISTS temp_test_table');
        $driver->modify(
            'CREATE TABLE `temp_test_table` (`id` INTEGER PRIMARY KEY, `field1` TEXT UNIQUE, `field2` TEXT, `field3` TEXT)'
        );
        $driver->modify('CREATE INDEX `test_index` ON `temp_test_table` ( `field2`, `field3` )');
    }

    protected function tearDownFixtures()
    {
        $this->getSqliteServer()
            ->getDbDriver()
            ->modify('DROP TABLE IF EXISTS temp_test_table');
    }

    public function testTableIndexes()
    {

        $table = $this->getSqliteServer()
            ->getDbDriver()
            ->getTable('temp_test_table');

        $indexes = $table->getIndexes();
        $this->assertEquals($indexes, $table->getIndexes(), 'Ожидается, что индексы загружаются 1 раз');

        $index = $table->getIndex('sqlite_autoindex_temp_test_table_1');
        $this->assertInstanceOf('umi\dbal\driver\IndexScheme', $index);

        $this->assertFalse($index->getIsDeleted());
        $this->assertTrue($index->getIsUnique());
        $this->assertFalse($index->getIsNew());
        $this->assertEquals('sqlite_autoindex_temp_test_table_1', $index->getName());
        $this->assertEquals(array('field1' => array('name' => 'field1', 'length' => null)), $index->getColumns());

        $this->assertNull($index->getType());
        $index->setType('HASH');
        $this->assertNull($index->getType(), 'Ожидается, что при выставлении типа индекса sqlite ничего не происходит');

        $index2 = $table->getIndex('test_index');
        $this->assertFalse($index2->getIsDeleted());
        $this->assertFalse($index2->getIsUnique());
        $this->assertFalse($index2->getIsNew());
        $this->assertEquals('test_index', $index2->getName());
        $this->assertEquals(
            array(
                'field2' => array('name' => 'field2', 'length' => null),
                'field3' => array('name' => 'field3', 'length' => null)
            ),
            $index2->getColumns()
        );

        $primaryKey = $table->getPrimaryKey();
        $this->assertInstanceOf('umi\dbal\driver\IndexScheme', $primaryKey);

        $this->assertFalse($primaryKey->getIsDeleted());
        $this->assertTrue($primaryKey->getIsUnique());
        $this->assertFalse($primaryKey->getIsNew());
        $this->assertEquals('PRIMARY', $primaryKey->getName());
        $this->assertEquals(array('id' => array('name' => 'id', 'length' => null)), $primaryKey->getColumns());

    }
}