<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\mysql;

use umi\dbal\driver\IColumnScheme;
use utest\dbal\DbalTestCase;

/**
 * Тестирование mysql драйвера
 * @package
 */
class MySqlTableTest extends DbalTestCase
{

    protected function tearDownFixtures()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->dropTable('temp_test_table');
        $dbDriver->dropTable('temp_test_related_table');
        $dbDriver->dropTable('temp_test_related_table2');
        $dbDriver->dropTable('temp_test_related_table3');
    }

    public function testExtract()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS temp_test_table (
                            `c` char(20) DEFAULT NULL
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT=\'тестовый комментарий\''
        );
        $table = $dbDriver->getTable('temp_test_table');

        $this->assertEquals('utf8_bin', $table->getCollation(), "Wrong collation");
        $this->assertEquals('utf8', $table->getCharset(), "Wrong charset");
        $this->assertEquals('InnoDB', $table->getEngine(), "Wrong engine");
        $this->assertEquals('тестовый комментарий', $table->getComment(), "Wrong comment");

    }

    public function testGenerateCreateQuery()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();

        $wrongTable = $dbDriver->addTable('wrong_table');
        $e = null;
        try {
            $wrongTable->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Ожидается исключение при получение миграционных запросов пустой таблицы'
        );

        $table = $dbDriver->addTable('test_query_table');
        $table->setEngine('InnoDB');
        $table->setCharset('utf8');
        $table->setCollation('utf8_bin');
        $table->setComment('тестовый комментарий');

        $table->addColumn('id', IColumnScheme::TYPE_SERIAL);

        $table->addColumn(
            'string',
            IColumnScheme::TYPE_VARCHAR,
            [
                IColumnScheme::OPTION_DEFAULT_VALUE => 'test',
                IColumnScheme::OPTION_COMMENT       => 'test',
                IColumnScheme::OPTION_COLLATION     => 'utf8_general_ci'
            ]
        );

        $table->addColumn('text', IColumnScheme::TYPE_TEXT, [IColumnScheme::OPTION_NULLABLE => true]);
        $table->addColumn(
            'domain',
            IColumnScheme::TYPE_INT,
            [IColumnScheme::OPTION_DEFAULT_VALUE => 0, IColumnScheme::OPTION_LENGTH => 10]
        );
        $table->addColumn('fulltext', IColumnScheme::TYPE_TEXT);

        $table->setPrimaryKey('id');
        $table->addIndex('text')
            ->addColumn('text', 255)
            ->setIsUnique(true);

        $table->addIndex('domain')
            ->addColumn('domain');
        $table->addIndex('id_domain')
            ->addColumn('id')
            ->addColumn('domain');

        $table->addIndex('fulltext')
            ->addColumn('fulltext')
            ->setType('FULLTEXT');

        $table->addConstraint('FK_test_Domain', 'domain', 'cms3_domains', 'id', 'SET NULL', 'CASCADE');

        $expectedResult = array(
            "CREATE TABLE `test_query_table` (
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`string` varchar(255) COLLATE utf8_general_ci DEFAULT 'test' COMMENT 'test',
	`text` text,
	`domain` int(10) DEFAULT '0',
	`fulltext` text,
	PRIMARY KEY (`id`),
	UNIQUE KEY `text` (`text`(255)),
	KEY `domain` (`domain`),
	KEY `id_domain` (`id`,`domain`),
	FULLTEXT KEY `fulltext` (`fulltext`),
	CONSTRAINT `FK_test_Domain` FOREIGN KEY (`domain`) REFERENCES `cms3_domains` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='тестовый комментарий'"
        );

        $this->assertEquals($expectedResult, $table->getMigrationQueries(), 'Wrong query built');
    }

    public function testGenerateAlterQuery()
    {

        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();

        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS `temp_test_related_table` (
                            `id` int(10) NOT NULL AUTO_INCREMENT,
                            PRIMARY KEY (`id`)
                        ) ENGINE=innoDB DEFAULT CHARSET=utf8'
        );

        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS `temp_test_related_table2` (
                            `id` int(10) NOT NULL AUTO_INCREMENT,
                            PRIMARY KEY (`id`)
                        ) ENGINE=innoDB DEFAULT CHARSET=utf8'
        );

        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS `temp_test_related_table3` (
                            `id` int(10) NOT NULL AUTO_INCREMENT,
                            PRIMARY KEY (`id`)
                        ) ENGINE=innoDB DEFAULT CHARSET=utf8'
        );

        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS temp_test_table (
                            `string` smallint(1) NOT NULL DEFAULT \'1\',
                            `word` varchar(255),
                            `word2` varchar(255),
                            `test` varchar(255),
                            `test_int` int(10) NOT NULL,
                            `test_int2` int(10) NOT NULL,
                            `test_int3` int(10) NOT NULL,
                            KEY `test_int` (`test_int`),
                            KEY `test` (`test`),
                            KEY `word2` (`word2`),
                            KEY `test_ints` (`test_int`, `test_int2`),
                            KEY `test_int3` (`test_int3`),
                            CONSTRAINT `FK_test_Int` FOREIGN KEY (`test_int`) REFERENCES `temp_test_related_table` (`id`),
                            CONSTRAINT `FK_test_Int2` FOREIGN KEY (`test_int2`) REFERENCES `temp_test_related_table2` (`id`)
                        )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci'
        );

        $table = $dbDriver->getTable('temp_test_table');

        $table->getColumn('string')
            ->setType(
            IColumnScheme::TYPE_INT,
            [IColumnScheme::OPTION_DEFAULT_VALUE => 10, IColumnScheme::OPTION_COMMENT => 'test']
        );

        $table->getColumn('word')
            ->setOption(IColumnScheme::OPTION_NULLABLE, false)
            ->setOption(IColumnScheme::OPTION_DEFAULT_VALUE, 10);

        $table->addColumn('name', IColumnScheme::TYPE_INT);

        $table->getColumn('test')
            ->setOption(IColumnScheme::OPTION_NULLABLE, true);

        $table->deleteColumn('test');

        $table->deleteIndex('test');
        $table->addIndex('word')
            ->addColumn('word', '10')
            ->setIsUnique(true);

        $index = $table->getIndex('word2');
        $index->setIsUnique(true);

        $index2 = $table->getIndex('test_ints');
        $index2->deleteColumn('test_int2');
        $index2->addColumn('word2');

        $table->addIndex('test_int3')
            ->addColumn('test_int3'); //проверяем, что добавление существующего индекса не вызывает лишних запросов

        $table->deleteConstraint('FK_test_Int');
        $table->addConstraint('FK_test_Int3', 'test_int', 'temp_test_related_table', 'id', 'CASCADE', 'CASCADE');
        $table->addConstraint('FK_test_Int2', 'test_int2', 'temp_test_related_table3', 'id', 'CASCADE', 'CASCADE');

        $table->setEngine('InnoDB');
        $table->setCharset('latin1');
        $table->setCollation('latin1_bin');
        $table->setComment('тестовый комментарий');

        $expectedResult = array(
            "ALTER TABLE `temp_test_table`
	DROP FOREIGN KEY `FK_test_Int`,
	DROP FOREIGN KEY `FK_test_Int2`,
	DROP KEY `test`,
	DROP KEY `word2`,
	DROP KEY `test_ints`,
	DROP `test`,
	ADD `name` int,
	MODIFY `string` int DEFAULT '10' COMMENT 'test',
	MODIFY `word` varchar(255) COLLATE utf8_general_ci NOT NULL DEFAULT '10',
	ADD UNIQUE KEY `word` (`word`(10)),
	ADD UNIQUE KEY `word2` (`word2`),
	ADD KEY `test_ints` (`test_int`,`word2`),
	ADD CONSTRAINT `FK_test_Int3` FOREIGN KEY (`test_int`) REFERENCES `temp_test_related_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ENGINE=InnoDB,
DEFAULT CHARACTER SET=latin1,
COLLATE latin1_bin,
COMMENT='тестовый комментарий'",
            "ALTER TABLE `temp_test_table`
	ADD CONSTRAINT `FK_test_Int2` FOREIGN KEY (`test_int2`) REFERENCES `temp_test_related_table3` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
        );

        $this->assertEquals($expectedResult, $table->getMigrationQueries(), 'Wrong query built');
    }

    public function testDropColumn()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS temp_test_table (rel_id int unsigned, tf float, word varchar(64))'
        );
        $table = $dbDriver->getTable('temp_test_table');

        $column = $table->getColumn('tf');
        $table->deleteColumn('tf');
        $this->assertTrue($column->getIsDeleted());

        $e = null;
        try {
            $table->deleteColumn('missed_column');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\NonexistentEntityException', $e);

    }

    public function testGenerateDropQuery()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS temp_test_table (rel_id int unsigned, tf float, word varchar(64))'
        );
        $table = $dbDriver->getTable('temp_test_table');
        $dbDriver->deleteTable('temp_test_table');
        $expectedResult = array("DROP TABLE IF EXISTS `temp_test_table`");

        $this->assertEquals($expectedResult, $table->getMigrationQueries(), 'Wrong query built');

    }

    public function testPrimaryKeyQueries()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS temp_test_table (id int(10), text varchar(255)) CHARACTER SET utf8'
        );
        $table = $dbDriver->getTable('temp_test_table');
        $table->setPrimaryKey('id');
        $table->getColumn('id')
            ->setOption(IColumnScheme::OPTION_AUTOINCREMENT, true);
        $table->setEngine('MyISAM');

        $expectedResult = array(
            'ALTER TABLE `temp_test_table`
	MODIFY `id` int(10) AUTO_INCREMENT,
	ADD PRIMARY KEY (`id`),
ENGINE=MyISAM,
DEFAULT CHARACTER SET=utf8,
COLLATE utf8_general_ci'
        );
        $this->assertEquals($expectedResult, $table->getMigrationQueries(), 'Wrong query built');
        $dbDriver->applyMigrations();

        $table = $dbDriver->getTable('temp_test_table');
        $table->setPrimaryKey('text');

        $expectedResult = array(
            'ALTER TABLE `temp_test_table`
	DROP PRIMARY KEY,
	MODIFY `id` int(10) NOT NULL,
	ADD PRIMARY KEY (`text`),
ENGINE=MyISAM,
DEFAULT CHARACTER SET=utf8,
COLLATE utf8_general_ci'
        );
        $this->assertEquals($expectedResult, $table->getMigrationQueries(), 'Wrong query built');
        $dbDriver->applyMigrations();

        $table = $dbDriver->getTable('temp_test_table');
        $table->deletePrimaryKey();
        $expectedResult = array(
            'ALTER TABLE `temp_test_table`
	DROP PRIMARY KEY,
ENGINE=MyISAM,
DEFAULT CHARACTER SET=utf8,
COLLATE utf8_general_ci'
        );
        $this->assertEquals($expectedResult, $table->getMigrationQueries(), 'Wrong query built');

    }

}
