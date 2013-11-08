<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\type\factory;

use umi\route\exception\RequiredDependencyException;
use umi\route\type\IRoute;

/**
 * Трейт для внедрения поддержки создания маршрутов.
 */
trait TRouteFactoryAware
{
    /**
     * @var IRouteFactory $_routeFactory фабрика
     */
    private $_routeFactory;

    /**
     * Устанавливает фабрику для создания маршрутов.
     * @param IRouteFactory $routeFactory фабрика
     */
    public final function setRouteFactory(IRouteFactory $routeFactory)
    {
        $this->_routeFactory = $routeFactory;
    }

    /**
     * Создает правила маршрутизатора на основе массива конфигурации.
     * @param array $config конфигурация
     * @return IRoute[] массив правил маршрутизатора
     */
    protected final function createRoutes(array $config)
    {
        return $this->getRouteFactory()
            ->createRoutes($config);
    }

    /**
     * Возвращает фабрику для создания маршрутов.
     * @return IRouteFactory
     * @throws RequiredDependencyException если фабрика не внедрена
     */
    private final function getRouteFactory()
    {
        if (!$this->_routeFactory) {
            throw new RequiredDependencyException(sprintf(
                'Route factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_routeFactory;
    }
}
