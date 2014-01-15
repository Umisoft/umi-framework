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
use umi\pagination\exception\RequiredDependencyException;

/**
 * Трейт для внедрения инструментов пагинатора.
 */
trait TPaginationAware
{
    /**
     * @var IPaginatorFactory $_paginationFactory фабрика пагинаторов
     */
    private $_paginationFactory;

    /**
     * {@inheritdoc}
     */
    public final function setPaginatorFactory(IPaginatorFactory $paginationFactory)
    {
        $this->_paginationFactory = $paginationFactory;
    }

    /**
     * Создает пагинатор,
     * выбирая адаптер автоматически на основе типа переданных объектов.
     * @param mixed $objects объекты
     * @param int $itemsPerPage количество элементов на странице
     * @return IPaginator созданный пагинатор
     */
    protected final function createObjectPaginator($objects, $itemsPerPage)
    {
        return $this->getPaginatorFactory()->createObjectPaginator($objects, $itemsPerPage);
    }

    /**
     * Создает пагинатор, используя заданный адаптер
     * @param IPaginationAdapter $adapter адаптер
     * @param int $itemsPerPage количество элементов на странице
     * @return IPaginator созданный пагинатор
     */
    protected final function createPaginator(IPaginationAdapter $adapter, $itemsPerPage)
    {
        return $this->getPaginatorFactory()->createPaginator($adapter, $itemsPerPage);
    }

    /**
     * Возвращает фабрику пагинаторов
     * @throws RequiredDependencyException если фабрика не была установлена
     * @return IPaginatorFactory
     */
    private function getPaginatorFactory()
    {
        if (!$this->_paginationFactory instanceof IPaginatorFactory) {
            throw new RequiredDependencyException(sprintf(
                'Paginator factory is not injected in class "%s".',
                get_class($this)
            ));
        }
        return $this->_paginationFactory;
    }
}