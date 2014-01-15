<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination\adapter;

use Traversable;

/**
 * Интерфейс адаптеров для пагинации.
 */
interface IPaginationAdapter
{
    /**
     * Имя адаптера для массивов.
     */
    const ARRAY_ADAPTER = 'array';

    /**
     * Возвращает общее количество элементов.
     * @return int
     */
    public function getTotal();

    /**
     * Возвращает список элементов для текущей страницы.
     * @param int $limit количество элементов
     * @param int $offset смещение
     * @return array|Traversable
     */
    public function getItems($limit, $offset);
}
