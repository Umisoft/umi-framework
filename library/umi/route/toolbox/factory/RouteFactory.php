<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\toolbox\factory;

use umi\route\exception\InvalidArgumentException;
use umi\route\exception\OutOfBoundsException;
use umi\route\IRouteFactory;
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
     * @var string $routerClass класс маршрутеризатора
     */
    public $routerClass = 'umi\route\Router';

    /**
     * {@inheritdoc}
     */
    public function createRouter(array $config)
    {
        $routes = $this->createRoutes($config);

        return $this->createInstance(
            $this->routerClass,
            [$routes],
            ['umi\route\IRouter']
        );
    }

    /**
     * Возвращает правило маршрутеризации на основе массива конфигурации.
     * @param array $config конфигурация
     * @throws \umi\route\exception\InvalidArgumentException если тип маршрута не передан
     * @throws \umi\route\exception\OutOfBoundsException если заданный тип маршрута не существует
     * @return IRoute правило маршрутизатора
     */
    protected function createRoute(array $config)
    {
        if (!isset($config[self::OPTION_TYPE])) {
            throw new InvalidArgumentException($this->translate(
                'Route type is not specified.'
            ));
        }

        $type = $config[self::OPTION_TYPE];
        if (!isset($this->types[$type])) {
            throw new OutOfBoundsException($this->translate(
                'Route type "{type}" is not available.',
                ['type' => $type]
            ));
        }

        $subroutes = [];

        if (isset($config[self::OPTION_SUBROUTES])) {
            $subroutes = $this->createRoutes($config[self::OPTION_SUBROUTES]);
        }

        unset($config[self::OPTION_TYPE]);
        unset($config[self::OPTION_SUBROUTES]);

        return $this->createInstance(
            $this->types[$type],
            [$config, $subroutes],
            ['umi\route\type\IRoute']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createRoutes(array $config)
    {
        $routes = [];

        foreach ($config as $name => $route) {
            $routes[$name] = $this->createRoute($route);
        }

        return $routes;
    }
}