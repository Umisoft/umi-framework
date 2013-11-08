<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\helper\paginator;

use umi\pagination\adapter\ArrayPaginationAdapter;
use umi\pagination\Paginator;
use umi\templating\exception\InvalidArgumentException;
use umi\templating\extension\helper\type\paginator\PaginatorSlidingHelper;
use utest\CallableTestCase;

/**
 * Тестирования пагинатора типа "Sliding"
 */
class PaginatorSlidingHelperTest extends CallableTestCase
{

    /**
     *  Тест проверяет контекст, когда страница находится в середине списка страниц
     */
    public function testBasic()
    {
        $adapter = new ArrayPaginationAdapter(range(0, 99));
        $paginator = new Paginator($adapter, 10);
        $paginator->setCurrentPage(5);
        $Helper = new PaginatorSlidingHelper();
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
        $this->assertEquals(
            $context,
            $Helper($paginator, 3),
            'Ожидается, что контекст в случае, когда страница в середине списка, будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда хелперу передается четное количество страниц в ряду.
     */
    public function testEvenRange()
    {
        $adapter = new ArrayPaginationAdapter(range(0, 99));
        $paginator = new Paginator($adapter, 10);
        $paginator->setCurrentPage(5);
        $Helper = new PaginatorSlidingHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($paginator, 4),
            'Ожидается, что контекст для четного количества страниц в ряду будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда количество страниц меньше, чем может быть выведено в ряду.
     */
    public function testSmallPagesCount()
    {
        $adapter = new ArrayPaginationAdapter(range(0, 22));
        $paginator = new Paginator($adapter, 10);
        $paginator->setCurrentPage(2);
        $Helper = new PaginatorSlidingHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($paginator, 7),
            'Ожидается, что контекст для малого количества страниц будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда страница находится в начале списка страниц
     */
    public function testFirstRange()
    {
        $adapter = new ArrayPaginationAdapter(range(0, 99));
        $paginator = new Paginator($adapter, 10);
        $paginator->setCurrentPage(2);
        $Helper = new PaginatorSlidingHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($paginator, 5),
            'Ожидается, что контекст в случае, когда страница в начале списка, будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда страница находится в конце списка страниц.
     */
    public function testLastRange()
    {
        $adapter = new ArrayPaginationAdapter(range(0, 99));
        $paginator = new Paginator($adapter, 10);
        $paginator->setCurrentPage(9);
        $Helper = new PaginatorSlidingHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($paginator, 5),
            'Ожидается, что контекст для случая, когда ряд страница находится в конце списка, будет сформирован верно.'
        );
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function wrongPagesInRange()
    {
        $adapter = new ArrayPaginationAdapter(range(0, 99));
        $paginator = new Paginator($adapter, 10);
        $Helper = new PaginatorSlidingHelper();
        $Helper($paginator, -1);
    }

    /**
     * Возвращает массив включающий экземпляр тестируемого класса, тестируемый метод класса и набор типизированных параметров.
     * @return array
     */
    protected function getCallable()
    {
        return [
            new PaginatorSlidingHelper(),
            '__invoke',
            ['umi\pagination\IPaginator' => new Paginator(new ArrayPaginationAdapter(range(0, 99)), 10)]
        ];
    }
}
