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
use utest\TestCase;

/**
 * Тестирование mysql драйвера
 * @package
 */
class MySqlTest extends TestCase
{

    protected function tearDownFixtures()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->dropTable('temp_test_table');
    }

    public function testAddingColumnMigrationQueries()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS temp_test_table (
                            `string` varchar(255) NOT NULL DEFAULT \'test\',
                            `word` varchar(255)
                        )ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        $table = $dbDriver->getTable('temp_test_table');
        $table->addColumn('name', IColumnScheme::TYPE_INT);

        $expectedResult = "ALTER TABLE `temp_test_table`
	ADD `name` int,
ENGINE=InnoDB,
DEFAULT CHARACTER SET=utf8,
COLLATE utf8_general_ci";

        $actualResult = $dbDriver->getMigrationQueries();
        $this->assertTrue(isset($actualResult[0]), 'Table is not modified by adding a column');
        $this->assertEquals($expectedResult, $actualResult[0], 'Wrong migration query');

    }

    public function testEditingColumnMigrationQueries()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS temp_test_table (
                            `string` smallint(1) NOT NULL DEFAULT \'1\',
                            `word` varchar(255)
                        )ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        $table = $dbDriver->getTable('temp_test_table');
        $table->getColumn('string')
            ->setOption(IColumnScheme::OPTION_DEFAULT_VALUE, 10);

        $expectedResult = "ALTER TABLE `temp_test_table`
	MODIFY `string` smallint(1) NOT NULL DEFAULT '10',
ENGINE=InnoDB,
DEFAULT CHARACTER SET=utf8,
COLLATE utf8_general_ci";

        $actualResult = $dbDriver->getMigrationQueries();
        $this->assertTrue(isset($actualResult[0]), 'Table is not modified by editing a column');
        $this->assertEquals($expectedResult, $actualResult[0], 'Wrong migration query');

    }

    public function testAddingTableMigrationQueries()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $table = $dbDriver->addTable('test_query_table');
        $table->setEngine('InnoDB');
        $table->setCharset('utf8');
        $table->addColumn('name', IColumnScheme::TYPE_INT);

        $expectedResult = "CREATE TABLE `test_query_table` (
	`name` int
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

        $actualResult = $dbDriver->getMigrationQueries();
        $this->assertTrue(isset($actualResult[0]), 'No migration query for added table');
        $this->assertEquals($expectedResult, $actualResult[0], 'Wrong migration query');
    }

    public function testEditingTableMigrationQueries()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS temp_test_table (
                            `string` varchar(255) NOT NULL DEFAULT \'test\',
                            `word` varchar(255)
                        )ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        $table = $dbDriver->getTable('temp_test_table');
        $table->setEngine('MyIsam');
        $table->setCharset('latin1');
        $table->setCollation('latin1_bin');

        $expectedResult = "ALTER TABLE `temp_test_table`
ENGINE=MyIsam,
DEFAULT CHARACTER SET=latin1,
COLLATE latin1_bin";

        $actualResult = $dbDriver->getMigrationQueries();
        $this->assertTrue(isset($actualResult[0]), 'Table is not modified by editing properties');
        $this->assertEquals($expectedResult, $actualResult[0], 'Wrong migration query');

    }

    public function testBuildTruncateTable()
    {
        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $sql = $dbDriver->buildTruncateQuery('temp_test_table');
        $this->assertEquals('DELETE FROM `temp_test_table`', $sql);
    }

}
