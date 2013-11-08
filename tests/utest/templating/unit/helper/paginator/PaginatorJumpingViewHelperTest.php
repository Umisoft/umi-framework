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
use umi\pagination\exception\InvalidArgumentException;
use umi\pagination\IPaginator;
use umi\pagination\Paginator;
use umi\templating\extension\helper\type\paginator\PaginatorJumpingHelper;
use utest\CallableTestCase;

/**
 * Тестирования пагинатора типа "Jumping"
 */
class PaginatorJumpingHelperTest extends CallableTestCase
{

    /**
     * @var IPaginator $paginator
     */
    public $paginator;

    public function setUpFixtures()
    {
        $this->paginator = new Paginator(new ArrayPaginationAdapter(range(0, 99)), 10);
    }

    /**
     * Тест проверяет контекст, когда страница находится в середине списка страниц.
     */
    public function testBasic()
    {
        $this->paginator->setCurrentPage(5);
        $Helper = new PaginatorJumpingHelper();
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
            $Helper($this->paginator, 3),
            'Ожидается, что контекст в случае, когда страница в середине списка, будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда страница находится в начале списка страниц
     */
    public function testFirstRangePage()
    {
        $this->paginator->setCurrentPage(2);
        $Helper = new PaginatorJumpingHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($this->paginator, 3),
            'Ожидается, что контекст в случае, когда страница в начале списка, будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда страница находится в конце ряда страниц.
     */
    public function testLastInRangePage()
    {
        $this->paginator->setCurrentPage(3);
        $Helper = new PaginatorJumpingHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($this->paginator, 3),
            'Ожидается, что контекст в случае, когда страница в конце ряда, будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда страница является последней в списке страниц.
     */
    public function testLastPage()
    {
        $this->paginator->setCurrentPage(10);
        $Helper = new PaginatorJumpingHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($this->paginator, 3),
            'Ожидается, что контекст в случае, когда страница является последней, будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда страница находится в конце списка страниц.
     */
    public function testPreLastPage()
    {
        $this->adapter = new ArrayPaginationAdapter(range(0, 78));
        $this->paginator = new Paginator($this->adapter, 10);
        $this->paginator->setCurrentPage(8);
        $Helper = new PaginatorJumpingHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($this->paginator, 3),
            'Ожидается, что контекст в случае, когда страница в конце списка, будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда количество страниц меньше, чем может быть выведено в ряду.
     */
    public function testNotMuchPages()
    {
        $this->adapter = new ArrayPaginationAdapter(range(0, 20));
        $this->paginator = new Paginator($this->adapter, 10);
        $this->paginator->setCurrentPage(2);
        $Helper = new PaginatorJumpingHelper();
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
            'itemsCount'        => 21,
            'firstItemNumber'   => 11,
            'lastItemNumber'    => 20,
        ];
        $this->assertEquals(
            $context,
            $Helper($this->paginator, 5),
            'Ожидается, что контекст для малого количества страниц будет сформирован верно.'
        );
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function wrongPagesInRange()
    {
        $Helper = new PaginatorJumpingHelper();
        $Helper($this->paginator, -1);
    }

    /**
     * Возвращает массив включающий экземпляр тестируемого класса, тестируемый метод класса и набор типизированных параметров.
     * @return array
     */
    protected function getCallable()
    {
        return [
            new PaginatorJumpingHelper(),
            '__invoke',
            ['umi\pagination\IPaginator' => new Paginator(new ArrayPaginationAdapter(range(0, 99)), 10)]
        ];
    }
}
