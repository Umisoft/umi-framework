<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\mysql;

use utest\TestCase;

/**
 * Тестирование индексов mysql драйвера
 */
class MySqlIndexesTest extends TestCase
{

    protected function setUpFixtures()
    {

        $this->getMysqlServer()
            ->getDbDriver()
            ->modify('DROP TABLE IF EXISTS test_table_indexes');
        $this->getMysqlServer()
            ->getDbDriver()
            ->modify('DROP TABLE IF EXISTS test_table_primary');
        $this->getMysqlServer()
            ->getDbDriver()
            ->modify(
            '
            CREATE TABLE `test_table_indexes` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `uri` text,
                `fulltext` text,
                `domain` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `domain` (`domain`),
                KEY `uri` (`uri`(255)),
                FULLTEXT KEY `fulltext` (`fulltext`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8'
        );

        $this->getMysqlServer()
            ->getDbDriver()
            ->modify(
            '
            CREATE TABLE `test_table_primary` (
                `id1` int(11) unsigned NOT NULL,
                `id2` int(11) unsigned NOT NULL,
                `text` text,
                PRIMARY KEY (`id1`, `id2`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8'
        );

    }

    protected function tearDownFixtures()
    {
        $this->getMysqlServer()
            ->getDbDriver()
            ->modify('DROP TABLE IF EXISTS test_table_indexes');
        $this->getMysqlServer()
            ->getDbDriver()
            ->modify('DROP TABLE IF EXISTS test_table_primary');
    }

    public function testTablePrimaryKey()
    {
        $table = $this->getMysqlServer()
            ->getDbDriver()
            ->getTable('test_table_primary');
        $primaryKey = $table->getPrimaryKey();
        $this->assertInstanceOf('umi\dbal\driver\IIndexScheme', $primaryKey);

        $this->assertFalse($primaryKey->getIsDeleted());
        $this->assertTrue($primaryKey->getIsUnique());
        $this->assertFalse($primaryKey->getIsNew());
        $this->assertEquals('PRIMARY', $primaryKey->getName());
        $this->assertEquals(
            ['id1' => ['name' => 'id1', 'length' => null], 'id2' => ['name' => 'id2', 'length' => null]],
            $primaryKey->getColumns()
        );
    }

    public function testTableIndexes()
    {

        $table = $this->getMysqlServer()
            ->getDbDriver()
            ->getTable('test_table_indexes');

        $indexes = $table->getIndexes();
        $this->assertEquals($indexes, $table->getIndexes(), 'Ожидается, что индексы загружаются 1 раз');
        $this->assertCount(3, $indexes, 'Wrong quantity of table indexes');

        $e = null;
        try {
            $table->getIndex('missed_index');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\NonexistentEntityException', $e);

        $e = null;
        try {
            $table->deleteIndex('missed_index');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\NonexistentEntityException', $e);

        $table->deleteIndex('domain');
        $index1 = $table->getIndex('domain');
        $this->assertInstanceOf('umi\dbal\driver\IndexScheme', $index1);

        $fullTextIndex = $table->getIndex('fulltext');

        $this->assertTrue($index1->getIsDeleted());
        $this->assertFalse($index1->getIsUnique());
        $this->assertFalse($index1->getIsNew());

        $this->assertEquals('domain', $index1->getName());
        $this->assertEquals('FULLTEXT', $fullTextIndex->getType());
        $this->assertEquals('BTREE', $index1->getType());

        $this->assertEquals(array('domain' => array('name' => 'domain', 'length' => null)), $index1->getColumns());

        $table->addIndex('id_domain')
            ->addColumn('id')
            ->addColumn('domain');

        $index4 = $table->addIndex('uri');
        $this->assertInstanceOf('umi\dbal\driver\IndexScheme', $index4);
        $this->assertFalse($index4->getIsNew());

        $this->assertEquals(4, count($table->getIndexes()), 'Wrong quantity of table indexes');

        $index2 = $table->getIndex('id_domain');
        $this->assertFalse($index2->getIsDeleted());
        $this->assertFalse($index2->getIsUnique());
        $this->assertTrue($index2->getIsNew());
        $this->assertEquals('id_domain', $index2->getName());
        $this->assertEquals(
            array(
                'id' => array('name' => 'id', 'length' => null),
                'domain' => array('name' => 'domain', 'length' => null)
            ),
            $index2->getColumns()
        );

        $index3 = $table->getIndex('uri');
        $this->assertFalse($index3->getIsDeleted());
        $this->assertFalse($index3->getIsUnique());
        $this->assertFalse($index3->getIsNew());
        $this->assertEquals('uri', $index3->getName());
        $this->assertEquals(array('uri' => array('name' => 'uri', 'length' => 255)), $index3->getColumns());
    }

    public function testIndexesModifications()
    {

        $table = $this->getMysqlServer()
            ->getDbDriver()
            ->getTable('test_table_indexes');

        $index1 = $table->getIndex('fulltext');

        $this->assertFalse(
            $index1->getIsModified(),
            'Только что полученный из таблицы индекс, над которым не проводилось действий, не должен иметь флагов'
        );
        $this->assertFalse(
            $index1->getIsNew(),
            'Только что полученный из таблицы индекс, над которым не проводилось действий, не должен иметь флагов'
        );
        $this->assertFalse(
            $index1->getIsDeleted(),
            'Только что полученный из таблицы индекс, над которым не проводилось действий, не должен иметь флагов'
        );

        $index1 = $table->addIndex('fulltext');

        $this->assertFalse(
            $index1->getIsModified(),
            'Повторно добавленный в таблицу индекс, над которым не проводилось действий, не должен иметь флагов'
        );
        $this->assertFalse(
            $index1->getIsNew(),
            'Повторно добавленный в таблицу индекс, над которым не проводилось действий, не должен иметь флагов'
        );
        $this->assertFalse(
            $index1->getIsDeleted(),
            'Повторно добавленный в таблицу индекс, над которым не проводилось действий, не должен иметь флагов'
        );

        $index1->setType('fulltext');

        $this->assertFalse(
            $index1->getIsModified(),
            'Изменение типа индекса на его текущий тип не должно приводить к изменению его состояния'
        );
        $this->assertFalse(
            $index1->getIsNew(),
            'Изменение типа индекса на его текущий тип не должно приводить к изменению его состояния'
        );
        $this->assertFalse(
            $index1->getIsDeleted(),
            'Изменение типа индекса на его текущий тип не должно приводить к изменению его состояния'
        );

        $index1->setType(null);
        $this->assertTrue(
            $index1->getIsModified(),
            'Изменение типа индекса должно приводить к изменению его состояния'
        );

        $index3 = $table->getIndex('domain')
            ->addColumn('domain');

        $this->assertFalse(
            $index3->getIsModified(),
            'Добавление существующей в индексе колонки не должно приводить к изменению состояния индекса'
        );
        $this->assertFalse(
            $index3->getIsNew(),
            'Добавление существующей в индексе колонки не должно приводить к изменению состояния индекса'
        );
        $this->assertFalse(
            $index3->getIsDeleted(),
            'Добавление существующей в индексе колонки не должно приводить к изменению состояния индекса'
        );

        $index3->addColumn('uri');
        $this->assertTrue(
            $index3->getIsModified(),
            'Добавление в индекс новой колонки должно приводить к изменению состояния индекса'
        );

        $index2 = $table->getIndex('uri')
            ->addColumn('uri', 255);
        $this->assertFalse(
            $index2->getIsModified(),
            'Выставление длины существующей в индексе колонке, равное текущей, не должно приводить к изменению состояния индекса'
        );
        $this->assertFalse(
            $index2->getIsNew(),
            'Выставление длины существующей в индексе колонке, равное текущей, не должно приводить к изменению состояния индекса'
        );
        $this->assertFalse(
            $index2->getIsDeleted(),
            'Выставление длины существующей в индексе колонке, равное текущей, не должно приводить к изменению состояния индекса'
        );

        $index2->addColumn('uri', 26);
        $this->assertTrue(
            $index2->getIsModified(),
            'Выставление длины существующей в индексе колонке, отличной от текущей, должно приводить к изменению состояния индекса'
        );

    }

}