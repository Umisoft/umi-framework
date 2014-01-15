<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination;

use umi\pagination\adapter\IPaginationAdapter;

/**
 * Интерфейс фабрики для пагинатора.
 */
interface IPaginatorFactory
{
    /**
     * Создает пагинатор.
     * Адаптер выбирается автоматически, на основе типа переданных параметров.
     * @param mixed $objects объекты
     * @param int $itemsPerPage количество элементов на странице
     * @return IPaginator созданный пагинатор
     */
    public function createObjectPaginator($objects, $itemsPerPage);

    /**
     * Создает пагинатор на основе адаптера.
     * @param IPaginationAdapter $adapter адаптер
     * @param int $itemsPerPage количество элементов на странице
     * @return IPaginator созданный пагинатор
     */
    public function createPaginator(IPaginationAdapter $adapter, $itemsPerPage);
}