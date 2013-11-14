<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\unit\pagination;

use umi\pagination\exception\InvalidArgumentException;
use umi\pagination\exception\OutOfBoundsException;
use umi\pagination\exception\UnexpectedValueException;
use umi\pagination\IPaginator;
use umi\pagination\Paginator;
use utest\pagination\PaginationTestCase;
use utest\pagination\mock\adapter\ArrayPaginationAdapter;
use utest\pagination\mock\adapter\TraversablePaginatorAdapter;
use utest\pagination\mock\adapter\WrongPaginationAdapter;

/**
 * Тестирование пагинатора.
 */
class PaginatorTest extends PaginationTestCase
{

    /**
     * @var IPaginator $paginator
     */
    protected $paginator;

    public function setUp()
    {
        $this->paginator = new Paginator(new ArrayPaginationAdapter(), 10);
    }

    /**
     * Тестирование базовой функциональности пагинатора.
     */
    public function testBasic()
    {
        $this->assertEquals(
            1,
            $this->paginator->getCurrentPage(),
            'Ожидается, что по умолчанию текущая страница равна 1.'
        );
        $this->paginator->setCurrentPage(2);
        $this->assertEquals(2, $this->paginator->getCurrentPage(), 'Ожидается, что текущая страница равна 2.');
        $this->assertEquals(10, $this->paginator->getPagesCount(), 'Ожидвается, что количество страниц равно 10.');
        $this->assertEquals(
            100,
            $this->paginator->getItemsCount(),
            'Ожидается, что общее количество элементов равно 100.'
        );
        $this->assertEquals(
            10,
            $this->paginator->getItemsPerPage(),
            'Ожидается, что количество элементов на странице равно 10.'
        );
        $this->assertEquals(
            range(10, 19),
            $this->paginator->getPageItems(),
            'Ожидается, что элементы страницы будут сфомированы верно.'
        );
    }

    /**
     * Тестирование работы пагинатора, когда адаптер возвращает Traversable объект
     */
    public function testTraversablePaginatorAdapter()
    {
        $paginator = new Paginator(new TraversablePaginatorAdapter(), 10);
        $this->assertInstanceOf(
            '\Traversable',
            $paginator->getPageItems(),
            'Ожидается, что метод вернет оъект типа Traversable.'
        );
    }

    /**
     * @test Ожидается исключение, когда адаптер возвращает неправильный набор элементов.
     * @expectedException UnexpectedValueException
     */
    public function badAdapterTest()
    {
        $paginator = new Paginator(new WrongPaginationAdapter(), 10);
        $paginator->getPageItems();
    }

    /**
     * @test Ожидается исключение в случае, когда номер страницы оказывается за пределами количества страниц.
     * @expectedException OutOfBoundsException
     */
    public function wrongPage()
    {
        $this->paginator->setCurrentPage(25);
    }

    /**
     * @test Ожидается исключение в случае, когда количество элементов на сранице задано не верно.
     * @expectedException InvalidArgumentException
     */
    public function wrongItemsPerPage()
    {
        new Paginator(new WrongPaginationAdapter(), 0);
    }
}

?>