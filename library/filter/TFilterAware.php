<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\filter;

use umi\filter\exception\RequiredDependencyException;

/**
 * Трейт для компонентов, поддерживающих фильтрацию.
 */
trait TFilterAware
{
    /**
     * @var IFilterFactory $_filterTools инструменты для работы с фильтрами
     */
    private $_filterFactory;

    /**
     * Устанавливает фабрику для создания фильтров.
     * @param IFilterFactory $filterFactory фабрика
     */
    public final function setFilterFactory(IFilterFactory $filterFactory)
    {
        $this->_filterFactory = $filterFactory;
    }

    /**
     * Создает коллекцию фильтров на основе массива.
     * @example ['null' => []]
     * @param array $config конфигурация фильтров
     * @return IFilterCollection
     */
    protected final function createFilterCollection(array $config = [])
    {
        return $this->getFilterFactory()
            ->createFilterCollection($config);
    }

    /**
     * Создает фильтр определенного типа. Устанавливет ему опции (если переданы).
     * @param string $type тип фильтра
     * @param array $options опции фильтра
     * @return IFilter созданный фильтр
     */
    protected final function createFilter($type, array $options = [])
    {
        return $this->getFilterFactory()
            ->createFilter($type, $options);
    }

    /**
     * Возвращает фабрику фильтров.
     * @return IFilterFactory
     * @throws RequiredDependencyException если фабрика не внедрена
     */
    private final function getFilterFactory()
    {
        if (!$this->_filterFactory) {
            throw new RequiredDependencyException(sprintf(
                'Filter factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_filterFactory;
    }
}
