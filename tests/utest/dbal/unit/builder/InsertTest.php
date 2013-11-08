<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\builder;

use umi\dbal\builder\InsertBuilder;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\TestCase;

/**
 * Тест билдера UPDATE-запросов

 */
class InsertTest extends TestCase
{
    /**
     * @var InsertBuilder $query
     */
    protected $query;

    protected function setUpFixtures()
    {
        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);

        $this->query = new InsertBuilder($this->getDbServer()
            ->getDbDriver(), $queryBuilderFactory);
    }

    public function testInsertMethod()
    {
        $e = null;
        try {
            $this->query->getTableName();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Expected Exception if table name for insert is empty.'
        );

        $this->assertInstanceOf('umi\dbal\builder\IQueryBuilder', $this->query->insert('someTable'));

        $this->assertEquals('someTable', $this->query->getTableName());
        $this->assertFalse($this->query->getIsIgnore());

        $this->query->insert('someTable', true);
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

        $this->query->set('column1', ':column1')
            ->setPlaceholders('column2');

        $this->assertEquals(
            array(
                'column1' => ':column1',
                'column2' => ':column2'
            ),
            $this->query->getValues()
        );
    }

    public function testInsertOnDuplicateKeyUpdate()
    {
        $this->query->set('column1')
            ->set('column2')
            ->onDuplicateKey('column3', 'column4')
            ->onDuplicateKey('column5')
            ->set('column3', ':column3')
            ->set('column4', ':column4')
            ->set('column5', ':column5');

        $this->assertEquals(
            [
                'column1' => ':column1',
                'column2' => ':column2'
            ],
            $this->query->getValues()
        );

        $this->assertEquals(
            [
                'column3' => ':column3',
                'column4' => ':column4',
                'column5' => ':column5'
            ],
            $this->query->getOnDuplicateKeyValues()
        );

        $this->assertEquals(['column5'], $this->query->getOnDuplicateKeyColumns());
    }
}
