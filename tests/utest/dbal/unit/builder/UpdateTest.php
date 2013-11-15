<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\builder;

use umi\dbal\builder\UpdateBuilder;
use umi\dbal\driver\dialect\MySqlDialect;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\dbal\DbalTestCase;

/**
 * Тест билдера UPDATE-запросов

 */
class UpdateTest extends DbalTestCase
{
    /**
     * @var UpdateBuilder $query
     */
    protected $query;

    protected function setUpFixtures()
    {
        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);
        $this->query = new UpdateBuilder(
            $this
                ->getDbServer()
                ->getConnection(),
            new MySqlDialect(),
            $queryBuilderFactory
        );
    }

    public function testUpdateMethod()
    {
        $e = null;
        try {
            $this->query->getTableName();
        } catch (\Exception $e) {
        }

        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Expected Exception if table name for update is empty.'
        );

        $this->query->update('someTable');

        $this->assertEquals('someTable', $this->query->getTableName());
        $this->assertFalse($this->query->getIsIgnore());

        $this->query->update('someTable', true);
        $this->assertTrue($this->query->getIsIgnore());
    }

    public function testSetMethod()
    {
        $e = null;
        try {
            $this->query->getValues();
        } catch (\Exception $e) {
        }

        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Expected Exception if empty values for SET.'
        );

        $this->query
            ->set('column1', ':column1')
            ->setPlaceholders('column2');

        $this->assertEquals(
            [
                'column1' => ':column1',
                'column2' => ':column2'
            ],
            $this->query->getValues()
        );
    }

    public function testWhereAndLimitMethod()
    {
        $this->query
            ->where()
            ->expr('c', '=', 'd')
            ->limit(':limit');

        $where = $this->query->getWhereExpressionGroup();

        $this->assertInstanceOf('\umi\dbal\builder\ExpressionGroup', $where);
        $this->assertEquals('AND', $where->getMode());
        $this->assertEquals([['c', '=', 'd']], $where->getExpressions());
        $this->assertEquals(':limit', $this->query->getLimit());
    }

    public function testBindValues()
    {
        $this->query
            ->update('someTable')
            ->set('column1')
            ->bindInt(':column1', 1)
            ->set('column2')
            ->bindString(':column2', '1')
            ->set('column3')
            ->bindFloat(':column3', 1.2)
            ->set('column4')
            ->bindBool(':column4', true)
            ->set('column5')
            ->bindBlob(':column5', 'blob')
            ->set('column6')
            ->bindNull(':column6')
            ->set('column7')
            ->bindArray(':column7', [1, 2])
            ->set('column7')
            ->bindArray(':column7', [1, 2, 3])
            ->set('column8')
            ->bindExpression(':column8', '(SELECT `id` FROM `someTable2`)')
            ->set('column8')
            ->bindExpression(':column8', '(SELECT `id` FROM `someTable3`)');

        $this->assertEquals(
            [
                ':column1' => [1, \PDO::PARAM_INT],
                ':column2' => ['1', \PDO::PARAM_STR],
                ':column3' => [1.2, \PDO::PARAM_STR],
                ':column4' => [true, \PDO::PARAM_BOOL],
                ':column5' => ['blob', \PDO::PARAM_LOB],
                ':column6' => [null, \PDO::PARAM_NULL],
                ':column7' => [1, 2, 3],
                ':column8' => '(SELECT `id` FROM `someTable3`)'
            ],
            $this->query->getPlaceholderValues()
        );
    }
}
