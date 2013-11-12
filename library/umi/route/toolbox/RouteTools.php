<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\toolbox;

use umi\route\IRouteAware;
use umi\route\type\factory\IRouteFactory;
use umi\route\type\factory\IRouteFactoryAware;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов маршрутизации.
 */
class RouteTools implements IRouteTools
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'route';

    use TToolbox;

    /**
     * @var string $routeFactory класс фабрики правил маршрутеризации
     */
    public $routeFactoryClass = 'umi\route\toolbox\factory\RouteFactory';
    /**
     * @var string $routeFactory класс фабрики правил маршрутеризации
     */
    public $routerFactoryClass = 'umi\route\toolbox\factory\RouterFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'route',
            $this->routeFactoryClass,
            ['umi\route\type\factory\IRouteFactory']
        );

        $this->registerFactory(
            'router',
            $this->routerFactoryClass,
            ['umi\route\IRouterFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IRouteAware) {
            $object->setRouterFactory($this->getRouterFactory());
        }

        if ($object instanceof IRouteFactoryAware) {
            $object->setRouteFactory($this->getRouteFactory());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRouterFactory()
    {
        return $this->getFactory('router');
    }

    /**
     * Возвращает фабрику правил маршрутизатора.
     * @return IRouteFactory
     */
    protected function getRouteFactory()
    {
        return $this->getFactory('route');
    }
}