<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\mysql;

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\TableDiff;
use umi\dbal\driver\IColumnScheme;
use utest\dbal\DbalTestCase;

/**
 * Тестирование mysql драйвера
 * @package
 */
class MySqlTest extends DbalTestCase
{

    public function testAlterQuoting()
    {
        //todo testAlterQuoting
    }

    public function testCreateQuoting()
    {
        //todo testCreateQuoting
    }

    public function testUpdateQuoting()
    {
        //todo testUpdateQuoting
    }

    public function testDeleteQuoting()
    {
        //todo testDeleteQuoting
    }

    public function testSelectQuoting()
    {
        //todo testSelectQuoting
    }

    protected function tearDownFixtures()
    {
        $this
            ->getMysqlServer()
            ->getConnection()
            ->getSchemaManager()
            ->dropTable('temp_test_table');
    }

    /**
     * @param $table
     * @param $newTable
     * @internal param $dbDriver
     * @return string[]|boolean
     */
    protected function diff($table, $newTable)
    {
        $dbDriver = $this
            ->getMysqlServer()
            ->getConnection();
        $comparator = new Comparator();
        $diff = $comparator->diffTable($table, $newTable);
        if ($diff instanceof TableDiff) {
            $actualResult = $dbDriver
                ->getDatabasePlatform()
                ->getAlterTableSQL($diff);

            return $actualResult;
        } else {
            return $diff;
        }
    }
}
