<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination\adapter;

/**
 * Простой адаптер пагинации, использующий массив в качестве набора элементов.
 */
class ArrayPaginationAdapter implements IPaginationAdapter
{
    /**
     * @var array $items массив элементов
     */
    protected $items;

    /**
     * Конструктор.
     * @param array $items элементы
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems($limit, $offset)
    {
        return array_slice($this->items, $offset, $limit);
    }
}
