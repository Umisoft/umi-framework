<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\helper;

use umi\pagination\adapter\ArrayPaginationAdapter;
use umi\pagination\Paginator;
use umi\templating\exception\InvalidArgumentException;
use umi\templating\extension\helper\type\pagination\PaginationHelperCollection;
use umi\templating\extension\helper\type\paginator\PaginatorAllHelper;
use utest\TestCase;

/**
 * Тестирования пагинатора типа "All"
 */
class PaginationHelpersTest extends TestCase
{
    /**
     * @var Paginator $paginator
     */
    protected $paginator;

    /**
     * @var PaginationHelperCollection $helperCollection
     */
    protected $helperCollection;

    public function setUpFixtures()
    {
        $this->paginator = new Paginator(new ArrayPaginationAdapter(range(0, 99)), 10);

        $this->paginator->setCurrentPage(5);

        $this->helperCollection = new PaginationHelperCollection();
    }

    public function testAllPagination()
    {
        $context = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 5,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 4,
            'nextPage'          => 6,
            'pagesRange'        => range(1, 10),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 41,
            'lastItemNumber'    => 50,
        ];

        $this->assertEquals($context, $this->helperCollection->all($this->paginator));
    }

    public function testJumpingPagination()
    {
        $this->paginator->setCurrentPage(5);
        $context = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 5,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 4,
            'nextPage'          => 6,
            'pagesRange'        => range(4, 6),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 41,
            'lastItemNumber'    => 50,
        ];
        $this->assertEquals($context, $this->helperCollection->jumping($this->paginator, 3));

        $this->paginator->setCurrentPage(2);
        $firstRangeContext = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 2,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 1,
            'nextPage'          => 3,
            'pagesRange'        => range(1, 3),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 11,
            'lastItemNumber'    => 20,
        ];
        $this->assertEquals($firstRangeContext, $this->helperCollection->jumping($this->paginator, 3));

        $this->paginator->setCurrentPage(3);
        $lastInRangeContext = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 3,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 2,
            'nextPage'          => 4,
            'pagesRange'        => range(1, 3),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 21,
            'lastItemNumber'    => 30,
        ];
        $this->assertEquals($lastInRangeContext, $this->helperCollection->jumping($this->paginator, 3));

        $this->paginator->setCurrentPage(10);
        $lastPageContext = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 10,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 9,
            'nextPage'          => null,
            'pagesRange'        => [10],
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 91,
            'lastItemNumber'    => 100,
        ];
        $this->assertEquals($lastPageContext, $this->helperCollection->jumping($this->paginator, 3));

        $paginator = new Paginator(new ArrayPaginationAdapter(range(0, 78)), 10);
        $paginator->setCurrentPage(8);
        $preLastPageContext = [
            'firstPage'         => 1,
            'lastPage'          => 8,
            'currentPage'       => 8,
            'pagesCount'        => 8,
            'itemsPerPage'      => 10,
            'previousPage'      => 7,
            'nextPage'          => null,
            'pagesRange'        => range(7, 8),
            'currentItemsCount' => 9,
            'itemsCount'        => 79,
            'firstItemNumber'   => 71,
            'lastItemNumber'    => 79,
        ];
        $this->assertEquals($preLastPageContext, $this->helperCollection->jumping($paginator, 3));

        $paginator = new Paginator(new ArrayPaginationAdapter(range(0, 20)), 10);
        $paginator->setCurrentPage(2);
        $notMuchPagesContext = [
            'firstPage'         => 1,
            'lastPage'          => 3,
            'currentPage'       => 2,
            'pagesCount'        => 3,
            'itemsPerPage'      => 10,
            'previousPage'      => 1,
            'nextPage'          => 3,
            'pagesRange'        => range(1, 3),
            'currentItemsCount' => 10,
            'itemsCount'        => 21,
            'firstItemNumber'   => 11,
            'lastItemNumber'    => 20,
        ];
        $this->assertEquals($notMuchPagesContext, $this->helperCollection->jumping($paginator, 5));
    }

    public function testSlidingPagination()
    {
        $context = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 5,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 4,
            'nextPage'          => 6,
            'pagesRange'        => range(4, 6),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 41,
            'lastItemNumber'    => 50,
        ];
        $this->assertEquals($context, $this->helperCollection->sliding($this->paginator, 3));

        $evenRangeContext = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 5,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 4,
            'nextPage'          => 6,
            'pagesRange'        => range(4, 7),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 41,
            'lastItemNumber'    => 50,
        ];
        $this->assertEquals($evenRangeContext, $this->helperCollection->sliding($this->paginator, 4));

        $this->paginator->setCurrentPage(2);
        $firstRangeContext = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 2,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 1,
            'nextPage'          => 3,
            'pagesRange'        => range(1, 5),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 11,
            'lastItemNumber'    => 20,
        ];
        $this->assertEquals($firstRangeContext, $this->helperCollection->sliding($this->paginator, 5));

        $this->paginator->setCurrentPage(9);
        $lastRangeContext = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 9,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 8,
            'nextPage'          => 10,
            'pagesRange'        => range(6, 10),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 81,
            'lastItemNumber'    => 90,
        ];
        $this->assertEquals($lastRangeContext, $this->helperCollection->sliding($this->paginator, 5));

        $paginator = new Paginator(new ArrayPaginationAdapter(range(0, 22)), 10);
        $paginator->setCurrentPage(2);
        $smallPagesContext = [
            'firstPage'         => 1,
            'lastPage'          => 3,
            'currentPage'       => 2,
            'pagesCount'        => 3,
            'itemsPerPage'      => 10,
            'previousPage'      => 1,
            'nextPage'          => 3,
            'pagesRange'        => range(1, 3),
            'currentItemsCount' => 10,
            'itemsCount'        => 23,
            'firstItemNumber'   => 11,
            'lastItemNumber'    => 20,
        ];
        $this->assertEquals($smallPagesContext, $this->helperCollection->sliding($paginator, 7));
    }

    public function testElasticPagination()
    {
        $context = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 5,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 4,
            'nextPage'          => 6,
            'pagesRange'        => range(3, 7),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 41,
            'lastItemNumber'    => 50
        ];
        $this->assertEquals($context, $this->helperCollection->elastic($this->paginator, 5));

        $this->paginator->setCurrentPage(1);
        $firstPageContext = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 1,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => null,
            'nextPage'          => 2,
            'pagesRange'        => range(1, 3),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 1,
            'lastItemNumber'    => 10
        ];
        $this->assertEquals($firstPageContext, $this->helperCollection->elastic($this->paginator, 5));

        $this->paginator->setCurrentPage(5);
        $evenRangeContext = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 5,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 4,
            'nextPage'          => 6,
            'pagesRange'        => range(3, 8),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 41,
            'lastItemNumber'    => 50,
        ];
        $this->assertEquals($evenRangeContext, $this->helperCollection->elastic($this->paginator, 6));

        $this->paginator->setCurrentPage(10);
        $lastPageContext = [
            'firstPage'         => 1,
            'lastPage'          => 10,
            'currentPage'       => 10,
            'pagesCount'        => 10,
            'itemsPerPage'      => 10,
            'previousPage'      => 9,
            'nextPage'          => null,
            'pagesRange'        => range(8, 10),
            'currentItemsCount' => 10,
            'itemsCount'        => 100,
            'firstItemNumber'   => 91,
            'lastItemNumber'    => 100
        ];
        $this->assertEquals($lastPageContext, $this->helperCollection->elastic($this->paginator, 5));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function jumpingWrongPageInRange()
    {
        $this->helperCollection->jumping($this->paginator, -1);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function slidingWrongPageInRange()
    {
        $this->helperCollection->sliding($this->paginator, -1);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function elasticWrongPageInRange()
    {
        $this->helperCollection->sliding($this->paginator, -1);
    }

}
