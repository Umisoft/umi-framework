<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination;

use umi\pagination\exception\RequiredDependencyException;

/**
 * Трейт для внедрения инструментов пагинатора.
 */
trait TPaginationAware
{
    /**
     * @var IPaginatorFactory $_paginationFactory фабрика
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
     * Создает пагинатор.
     * Адаптер выбирается автоматически, на основе типа переданных параметров.
     * @param mixed $objects объекты
     * @param int $itemsPerPage количество элементов на странице
     * @throws RequiredDependencyException если инструменты не были установлены
     * @return IPaginator созданный пагинатор
     */
    protected final function createPaginator($objects, $itemsPerPage)
    {
        if (!$this->_paginationFactory instanceof IPaginatorFactory) {
            throw new RequiredDependencyException(sprintf(
                'Paginator factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_paginationFactory->createPaginator($objects, $itemsPerPage);
    }
}