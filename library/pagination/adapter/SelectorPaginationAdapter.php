<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination\adapter;

use umi\orm\selector\ISelector;

/**
 * Адаптер пагинации для selector'а.
 */
class SelectorPaginationAdapter implements IPaginationAdapter
{
    /**
     * @var ISelector $selector селектор
     */
    protected $selector;

    /**
     * Конструктор.
     * @param ISelector $selector селектор
     */
    public function __construct(ISelector $selector)
    {
        $this->selector = $selector;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        $totalSelector = clone $this->selector; // todo: is it hack?
        return $totalSelector->getTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function getItems($limit, $offset)
    {
        return $this->selector
            ->limit($limit, $offset)
            ->result();
    }
}