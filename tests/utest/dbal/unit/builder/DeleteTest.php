<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\builder;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use umi\dbal\builder\DeleteBuilder;
use umi\dbal\driver\IDialect;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\dbal\DbalTestCase;

/**
 * Тест билдера DELETE-запросов
 */
class DeleteTest extends DbalTestCase
{
    /**
     * @var DeleteBuilder $query
     */
    protected $query;

    protected function setUpFixtures()
    {
        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);

        /** @var $dialect IDialect */
        $dialect = $this
            ->getDbServer()
            ->getConnection()
            ->getDatabasePlatform();
        $this->query = new DeleteBuilder(
            $this->getDbServer()->getConnection(),
            $dialect,
            $queryBuilderFactory
        );
    }

    public function testFromMethod()
    {
        $e = null;
        try {
            $this->query->getTableName();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            '\umi\dbal\exception\RuntimeException',
            $e,
            'Expected RuntimeException if table name for delete is empty.'
        );

        $this->assertInstanceOf('umi\dbal\builder\IQueryBuilder', $this->query->from('someTable'));
        $this->assertEquals('someTable', $this->query->getTableName());
    }

    public function testWhereAndLimitMethod()
    {
        $this->query
            ->where()
            ->expr('x', '!=', 'y')
            ->limit(':limit');

        $where = $this->query->getWhereExpressionGroup();

        $this->assertInstanceOf('\umi\dbal\builder\ExpressionGroup', $where);
        $this->assertEquals('AND', $where->getMode());
        $this->assertEquals(
            [
                ['x', '!=', 'y']
            ],
            $where->getExpressions()
        );

        $this->assertEquals(':limit', $this->query->getLimit());
    }

    public function testOrders()
    {
        $this->query
            ->orderBy('field1')
            ->orderBy('field2', 'ASC')
            ->orderBy('field2', 'DESC')
            ->orderBy('field3', 'ASC')
            ->orderBy('field5', 'Invalid direction');
        $expected = [
            'field1' => 'ASC',
            'field2' => 'DESC',
            'field3' => 'ASC'
        ];
        $this->assertEquals($expected, $this->query->getOrderConditions());
    }
}
