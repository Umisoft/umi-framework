<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination;

use Traversable;
use umi\i18n\TLocalizable;
use umi\pagination\adapter\IPaginationAdapter;
use umi\pagination\exception\InvalidArgumentException;
use umi\pagination\exception\OutOfBoundsException;
use umi\pagination\exception\UnexpectedValueException;

/**
 * Класс пагинатора.
 */
class Paginator implements IPaginator
{

    use TLocalizable;

    /**
     * @var int $itemsPerPage количество элементов на странице
     */
    protected $itemsPerPage;
    /**
     * @var int $currentPage номер текущей страницы
     */
    protected $currentPage = 1;
    /**
     * @var IPaginationAdapter $adapter адаптер пагинатора
     */
    protected $adapter;

    /**
     * Конструктор.
     * @param IPaginationAdapter $adapter адаптер пагинатора
     * @param int $itemsPerPage количество элементов на странице
     * @throws InvalidArgumentException если количество страниц в ряду задано неверно
     */
    public function __construct(IPaginationAdapter $adapter, $itemsPerPage)
    {
        if ($itemsPerPage <= 0) {
            throw new InvalidArgumentException($this->translate(
                'Items per page should be positive.'
            ));
        }

        $this->itemsPerPage = $itemsPerPage;
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentPage($page)
    {
        if ($page > $this->getPagesCount() || $page < 1) {
            throw new OutOfBoundsException($this->translate(
                'Current page({value}) should be between 1 and pages count({count}).',
                ['value' => $page, 'count' => $this->getPagesCount()]
            ));
        }

        $this->currentPage = $page;
    }

    /**
     * {@inheritdoc}
     */
    public function getPagesCount()
    {
        return ceil($this->adapter->getTotal() / $this->itemsPerPage);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsCount()
    {
        return $this->adapter->getTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function getPageItems()
    {
        $offset = ($this->currentPage - 1) * $this->itemsPerPage;
        $items = $this->adapter->getItems($this->itemsPerPage, $offset);
        if (!is_array($items) && (!$items instanceof Traversable)) {
            throw new UnexpectedValueException($this->translate(
                'Paginator adapter should return array or Traversable items.'
            ));
        }

        return $items;
    }
}
