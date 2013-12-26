<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\pagination\mock\adapter;

use umi\pagination\adapter\IPaginationAdapter;

/**
 * Мок-адаптер для пагинатора на основе простого массива
 */
class MockArrayPaginationAdapter implements IPaginationAdapter
{

    /**
     * Возвращает количество элементов
     * @return int
     */
    public function getTotal()
    {
        return 100;
    }

    /**
     * Возвращает список элементов для текущей страницы
     * @param int $limit ограничение
     * @param int $offset смещение
     * @return array
     */
    public function getItems($limit, $offset)
    {
        return range($offset, $offset + $limit - 1);
    }
}