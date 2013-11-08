<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\unit\pagination\adapter;

use umi\pagination\adapter\ArrayPaginationAdapter;
use utest\TestCase;

/**
 * Тестирование ArrayPaginatorAdapterTest.
 */
class ArrayPaginationAdapterTest extends TestCase
{

    /**
     * Базовый тест.
     */
    public function testBasic()
    {
        $adapter = new ArrayPaginationAdapter(range(0, 99));
        $this->assertEquals($adapter->getTotal(), 100, 'Ожидается, что количество элементов в адаптере равно 100.');
        $this->assertEquals(
            range(11, 20),
            $adapter->getItems(10, 11),
            'Ожидается, что список элементов будет сформирован верно.'
        );
    }
}
