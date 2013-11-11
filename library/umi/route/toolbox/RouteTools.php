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
use umi\route\IRouteFactory;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов маршрутизации.
 */
class RouteTools implements IRouteTools
{
    use TToolbox;

    /**
     * @var string $routeFactory класс фабрики правил маршрутеризации
     */
    public $routeFactoryClass = 'umi\route\toolbox\factory\RouteFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'route',
            $this->routeFactoryClass,
            ['umi\route\IRouteFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IRouteAware) {
            $object->setRouteFactory($this->getRouteFactory());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteFactory()
    {
        return $this->getFactory('route');
    }
}