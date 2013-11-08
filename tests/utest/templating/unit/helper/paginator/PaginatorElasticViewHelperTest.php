<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\helper\paginator;

use umi\pagination\adapter\IPaginationAdapter;
use umi\pagination\IPaginator;
use umi\pagination\Paginator;
use umi\templating\exception\InvalidArgumentException;
use umi\templating\extension\helper\type\paginator\PaginatorElasticHelper;
use utest\CallableTestCase;
use utest\pagination\mock\adapter\ArrayPaginationAdapter;

/**
 * Тестирования пагинатора типа "Elastic"
 */
class PaginatorElasticHelperTest extends CallableTestCase
{

    /**
     * @var IPaginationAdapter $adapter
     */
    public $adapter;
    /**
     * @var IPaginator $paginator
     */
    public $paginator;

    public function setUpFixtures()
    {
        $this->adapter = new ArrayPaginationAdapter(range(0, 99));
        $this->paginator = new Paginator($this->adapter, 10);
    }

    /**
     * Тест Elastic вида пагинатора для первой страницы
     */
    public function testTheFirstPage()
    {
        $this->paginator->setCurrentPage(1);
        $Helper = new PaginatorElasticHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($this->paginator, 5),
            'Ожидается, что контекст для случая, когда страница является первой в списке, будет создан корректно.'
        );
    }

    /**
     * Тест проверяет контекст, когда хелперу передается четное количество страниц в ряду.
     */
    public function testEvenRange()
    {
        $this->paginator->setCurrentPage(5);
        $Helper = new PaginatorElasticHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($this->paginator, 6),
            'Ожидается, что контекст для четного количества страниц в ряду будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда страница является последней в списке страниц.
     */
    public function testElasticStylingOnLastPage()
    {
        $this->paginator->setCurrentPage(10);
        $Helper = new PaginatorElasticHelper();
        $context = [
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
        $this->assertEquals(
            $context,
            $Helper($this->paginator, 5),
            'Ожидается, что контекст в случае, когда страница является последней, будет сформирован верно.'
        );
    }

    /**
     * Тест проверяет контекст, когда страница находится в середине списка страниц.
     */
    public function testElasticStyling()
    {
        $this->paginator->setCurrentPage(5);
        $Helper = new PaginatorElasticHelper();
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
        $this->assertEquals(
            $context,
            $Helper($this->paginator, 5),
            'Ожидается, что контекст в случае, когда страница в середине списка, будет сформирован верно.'
        );
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function wrongPagesInRange()
    {
        $Helper = new PaginatorElasticHelper();
        $Helper($this->paginator, -1);
    }

    /**
     * @inheritdoc
     */
    protected function getCallable()
    {
        return [
            new PaginatorElasticHelper(),
            '__invoke',
            ['umi\pagination\IPaginator' => new Paginator(new ArrayPaginationAdapter(range(0, 99)), 10)]
        ];
    }
}
