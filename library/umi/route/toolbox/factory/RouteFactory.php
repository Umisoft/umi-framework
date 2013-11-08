<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\toolbox\factory;

use umi\route\exception\InvalidArgumentException;
use umi\route\exception\OutOfBoundsException;
use umi\route\type\factory\IRouteFactory;
use umi\route\type\IRoute;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика правил для маршрутизатора.
 */
class RouteFactory implements IRouteFactory, IFactory
{

    use TFactory;

    /**
     * @var array $routeTypes типы правил маршрутизатора
     */
    public $types = [
        self::ROUTE_FIXED    => 'umi\route\type\FixedRoute',
        self::ROUTE_REGEXP   => 'umi\route\type\RegexpRoute',
        self::ROUTE_SIMPLE   => 'umi\route\type\SimpleRoute',
        self::ROUTE_EXTENDED => 'umi\route\type\ExtendedRoute'
    ];

    /**
     * Возвращает правило маршрутеризации на основе массива конфигурации.
     * @param array $config конфигурация
     * @throws InvalidArgumentException
     * @return IRoute правило маршрутизатора
     */
    protected function createRoute(array $config)
    {
        if (!isset($config['type'])) {
            throw new InvalidArgumentException($this->translate(
                'Route type is not specified.'
            ));
        }

        $type = $config['type'];
        if (!isset($this->types[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Route type "{type}" is not available.',
                ['type' => $type]
            ));
        }

        $subroutes = [];

        if (isset($config['subroutes'])) {
            $subroutes = $this->createRoutes($config['subroutes']);
        }

        unset($config['type']);
        unset($config['subroutes']);

        return $this->createInstance(
            $this->types[$type],
            [$subroutes],
            ['umi\route\type\IRoute'],
            $config
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createRoutes(array $config)
    {
        $routes = [];

        foreach ($config as $name => $route) {
            $routes[$name] = $this->createRoute($route);
        }

        return $routes;
    }
}