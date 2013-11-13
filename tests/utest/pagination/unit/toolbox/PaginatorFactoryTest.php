<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination;

use umi\pagination\exception\InvalidArgumentException;
use umi\pagination\toolbox\factory\PaginatorFactory;
use umi\toolkit\exception\DomainException;
use utest\TestCase;

/**
 * Тесты фабрики пагинаторов.
 */
class PaginatorFactoryTest extends TestCase
{
    /**
     * @var PaginatorFactory $paginator
     */
    private $factory;

    public function setUpFixtures()
    {
        $this->factory = $this->getTestToolkit()->getService('umi\pagination\IPaginatorFactory');
    }

    public function testPaginatorTools()
    {
        $paginator = $this->factory->createPaginator(['object1', 'object2'], 25);

        $this->assertInstanceOf('umi\pagination\IPaginator', $paginator, 'Ожидается, что будет получен пагинатор.');
        $this->assertEquals(
            25,
            $paginator->getItemsPerPage(),
            'Ожидаетя, что количество элементов на странице будет задано верно.'
        );
    }

    /**
     * @test неподдерживаемого типа объекта.
     * @expectedException InvalidArgumentException
     */
    public function wrongPaginatorObject()
    {
        $this->factory->createPaginator(new \StdClass(), 25);
    }

    /**
     * @test
     * @expectedException DomainException
     */
    public function wrongPaginatorClass()
    {
        $this->factory->paginatorClass = '\StdClass';
        $this->factory->createPaginator(['object1', 'object2'], 25);
    }
}