<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\filter\unit\toolbox;

use umi\filter\exception\OutOfBoundsException;
use umi\filter\IFilterFactory;
use umi\filter\toolbox\factory\FilterFactory;
use utest\filter\FilterTestCase;

/**
 * Тесты инструментов для фильтрации
 */
class FilterFactoryTest extends FilterTestCase
{
    /**
     * @var IFilterFactory $tools инструментарий фильтров
     */
    protected $factory = null;

    public function setUpFixtures()
    {
        $this->factory = new FilterFactory();
        $this->resolveOptionalDependencies($this->factory);

        $this->factory->types = [
            'mock'  => 'utest\filter\mock\FilterFixture2',
            'mock2' => 'utest\filter\mock\FilterFixture2',
        ];
        $this->factory->filterCollectionClass = 'utest\filter\mock\FilterCollectionFixture';
    }

    public function testFilterCollectionCreation()
    {
        $filter = $this->factory->createFilterCollection(
            [
                'mock' => ['settings' => 'test']
            ]
        );
        $this->assertInstanceOf(
            'utest\filter\mock\FilterCollectionFixture',
            $filter,
            "Ожидается, что будет создан фильтр"
        );

        $filterCollection = $this->factory->createFilterCollection(
            [
                'mock'  => [],
                'mock2' => []
            ]
        );
        $this->assertInstanceOf(
            'utest\filter\mock\FilterCollectionFixture',
            $filterCollection,
            "Ожидается, что будет создана коллекция фильтров"
        );
    }

    public function testFilterCreation()
    {
        $filter = $this->factory->createFilter('mock', ['settings' => 'test']);
        $this->assertInstanceOf(
            'utest\filter\mock\FilterFixture2',
            $filter,
            "Ожидается, что будет создан фильтр."
        );
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function wrongFilterType()
    {
        $this->factory->createFilter('mock3');
    }
}