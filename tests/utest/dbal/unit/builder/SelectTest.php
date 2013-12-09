<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\builder;

use umi\dbal\builder\ExpressionGroup;
use umi\dbal\builder\IExpressionGroup;
use umi\dbal\builder\SelectBuilder;
use umi\dbal\driver\dialect\MySqlDialect;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\dbal\DbalTestCase;

/**
 * Тест билдера SELECT-запросов
 */
class SelectTest extends DbalTestCase
{
    /**
     * @var SelectBuilder $query
     */
    protected $query;

    protected function setUpFixtures()
    {
        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);

        $this->query = new SelectBuilder(
            $this->getDbServer()->getConnection(),
            new MySqlDialect(),
            $queryBuilderFactory
        );
    }

    public function testSelectMethod()
    {
        $what = $this->query->select([
            'field1',
            ['field1', 'fld1'],
            ['field2', 'fld2'],
            ['field3', 'fld3'],
            ['tbl_name.field4', 'fld4'],
            ['(SELECT subfield as subalias FROM subtable)', 'fld5']
        ]);

        $this->assertInstanceOf('umi\dbal\builder\IQueryBuilder', $what);

        $expectedResult = [
            ['field1', null],
            ['field1', 'fld1'],
            ['field2', 'fld2'],
            ['field3', 'fld3'],
            ['tbl_name.field4', 'fld4'],
            ['(SELECT subfield as subalias FROM subtable)', 'fld5']
        ];

        $this->assertEquals($expectedResult, $this->query->getSelectColumns(), 'SelectBuilder::select(a,b,c) failed');

        // test SELECT_ALL mode
        $this->query->select();
        $this->assertEmpty($this->query->getSelectColumns());
    }

    public function testFromMethod()
    {

        $this->assertInstanceOf(
            'umi\dbal\builder\IQueryBuilder',
            $this->query->from(['table1', 'table1 as tbl1', ['table2', 'tbl2']])
        );

        $expectedResult = [
            ['table1', null],
            ['table1', 'tbl1'],
            ['table2', 'tbl2']
        ];
        $this->assertEquals($expectedResult, $this->query->getTables(), 'SelectBuilder::from(a,b,c) failed');

        $e = null;
        try {
            $this->query->from();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\RuntimeException', $e, 'Exception for empty from expected.');
    }

    public function testDistinctMethod()
    {
        $this->assertFalse($this->query->getDistinct());
        $this->assertInstanceOf('umi\dbal\builder\IQueryBuilder', $this->query->distinct(true));
        $this->assertTrue($this->query->getDistinct());
    }

    public function testCache()
    {
        $this->assertFalse($this->query->getCacheDisabled());
        $this->assertInstanceOf('umi\dbal\builder\IQueryBuilder', $this->query->disableCache());
        $this->assertTrue($this->query->getCacheDisabled());
    }

    public function testLimitOffset()
    {
        $this->assertEquals(0, $this->query->getLimit());
        $this->assertEquals(0, $this->query->getOffset());
        $this->assertFalse($this->query->getUseCalcFoundRows());

        $this->assertInstanceOf('umi\dbal\builder\IQueryBuilder', $this->query->limit(5));
        $this->assertEquals(5, $this->query->getLimit());
        $this->assertEquals(0, $this->query->getOffset());
        $this->assertFalse($this->query->getUseCalcFoundRows());

        $this->query->limit(100);
        $this->assertInstanceOf('umi\dbal\builder\IQueryBuilder', $this->query->offset(200));
        $this->assertEquals(100, $this->query->getLimit());
        $this->assertEquals(200, $this->query->getOffset());

        $this->query->limit(200, 100, true);
        $this->assertEquals(200, $this->query->getLimit());
        $this->assertEquals(100, $this->query->getOffset());
        $this->assertTrue($this->query->getUseCalcFoundRows());
    }

    public function testJoins()
    {

        $this->assertInstanceOf('umi\dbal\builder\IQueryBuilder', $this->query->join('joinTable as table', 'LEFT'));
        $this->assertInstanceOf(
            'umi\dbal\builder\IQueryBuilder',
            $this->query
                ->on('table1.id', '=', 'table2.id')
                ->on('table1.field', '=', 'table2.field')
        );

        $this->query
            ->join('innerJoinTable as table2')
            ->on('table2.id', '=', 'table1.id');

        $this->query
            ->innerJoin('innerJoinTable2')
            ->on('innerJoinTable2.id', '=', 'table1.id');

        $this->query
            ->leftJoin('leftJoinTable2')
            ->on('leftJoinTable2.id', '=', 'table1.id');

        $joins = $this->query->getJoins();

        $join = $joins['table'];
        $this->assertInstanceOf('\umi\dbal\builder\JoinBuilder', $join);
        $this->assertEquals(['joinTable', 'table'], $join->getTable());
        $this->assertEquals('LEFT', $join->getType());

        $expected = [
            ['table1.id', '=', 'table2.id'],
            ['table1.field', '=', 'table2.field']
        ];
        $this->assertEquals($expected, $join->getConditions());

        $this->assertEquals(['innerJoinTable', 'table2'], $joins['table2']->getTable());
        $this->assertEquals('INNER', $joins['table2']->getType());

        $this->assertEquals(['innerJoinTable2', null], $joins['innerJoinTable2']->getTable());
        $this->assertEquals('INNER', $joins['innerJoinTable2']->getType());

        $this->assertEquals(['leftJoinTable2', null], $joins['leftJoinTable2']->getTable());
        $this->assertEquals('LEFT', $joins['leftJoinTable2']->getType());
    }

    public function testOrders()
    {
        $this->query
            ->orderBy('field1')
            ->orderBy('field2', 'ASC')
            ->orderBy('field2', 'DESC')
            ->orderBy('field3', 'ASC')
            ->orderBy('field5', 'Invalid direction');
        $expected = array(
            'field1' => 'ASC',
            'field2' => 'DESC',
            'field3' => 'ASC'
        );
        $this->assertEquals($expected, $this->query->getOrderConditions());
    }

    public function testWhereConditions()
    {

        $this->query
            ->begin(IExpressionGroup::MODE_AND)
            ->end();
        $this->assertNull($this->query->getWhereExpressionGroup());

        $e = null;
        try {
            $this->query->expr('a', '=', 'b');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Ожидается, что нельзя задавать выражения, если не начата группа выражений'
        );

        $this->query
            ->where('AND')
            ->expr('c', '=', 'd')
            ->begin()
            ->expr('a', '=', 'b')
            ->begin('OR')
            ->expr('e', '=', 'x')
            ->expr('x', '=', 'y')
            ->end()
            ->begin('OR')
            ->expr('f', '>', 'h')
            ->end()
            ->end()
            ->expr('j', '!=', 'b')
            ->having('OR')
            ->expr('boo', '!=', ':boo')
            ->expr('moo', '=', '100500');

        $where = $this->query->getWhereExpressionGroup();
        $this->assertInstanceOf('\umi\dbal\builder\ExpressionGroup', $where);
        $this->assertEquals('AND', $where->getMode());

        $this->assertEquals(
            [
                ['c', '=', 'd'],
                ['j', '!=', 'b'],
            ],
            $where->getExpressions()
        );

        $groups1 = $where->getGroups();
        $this->assertCount(1, $groups1);
        $this->assertInstanceOf('\umi\dbal\builder\ExpressionGroup', $groups1[0]);
        /**
         * @var ExpressionGroup $group1
         */
        $group1 = $groups1[0];
        $this->assertEquals('AND', $group1->getMode());
        $this->assertEquals(
            [
                ['a', '=', 'b']
            ],
            $group1->getExpressions()
        );

        $groups2 = $group1->getGroups();
        $this->assertCount(2, $groups2);
        $this->assertInstanceOf('\umi\dbal\builder\ExpressionGroup', $groups2[0]);
        /**
         * @var ExpressionGroup $group2
         */
        $group2 = $groups2[0];
        $this->assertEquals('OR', $group2->getMode());
        $this->assertEquals(
            [
                ['e', '=', 'x'],
                ['x', '=', 'y']
            ],
            $group2->getExpressions()
        );

        // Having
        $having = $this->query->getHavingExpressionGroup();
        $this->assertInstanceOf('\umi\dbal\builder\ExpressionGroup', $having);
        $this->assertEquals('OR', $having->getMode());
        $this->assertEquals(
            [
                ['boo', '!=', ':boo'],
                ['moo', '=', '100500'],
            ],
            $having->getExpressions()
        );
    }

    public function testGroupBy()
    {
        $this->query
            ->groupBy('field1')
            ->groupBy('field2', 'ASC')
            ->groupBy('field2', 'DESC')
            ->groupBy('field3', 'ASC')
            ->groupBy('field5', 'Invalid direction');
        $expected = [
            'field1' => 'ASC',
            'field2' => 'DESC',
            'field3' => 'ASC'
        ];

        $this->assertEquals($expected, $this->query->getGroupByConditions());
    }
}
