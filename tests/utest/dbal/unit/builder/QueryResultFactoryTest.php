<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\builder;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use utest\dbal\DbalTestCase;

/**
 * Тест фабрики построителей запросов
 */
class QueryResultFactoryTest extends DbalTestCase
{
    protected $affectedTables = ['temp_test_table'];

    protected function setUpFixtures()
    {
        $table = new Table('temp_test_table',[
            new Column('id',Type::getType(Type::INTEGER)),
            new Column('field1',Type::getType(Type::TEXT), ['notnull'=>false]),
            new Column('field2',Type::getType(Type::TEXT), ['notnull'=>false]),
        ]);
        $this->connection->getSchemaManager()->createTable($table);
    }

    public function testResultBuilderFactory()
    {
        $builder = $this->getDefaultDbServer()
            ->select()
            ->from('temp_test_table');
        $result = $builder->execute();
        $this->assertInstanceOf(
            'Doctrine\DBAL\Driver\ResultStatement',
            $result,
            'Ожидается, что IQueryBuilder->execute() вернет IQueryResult'
        );
    }
}
