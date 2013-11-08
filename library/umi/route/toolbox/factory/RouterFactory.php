<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\toolbox\factory;

use umi\route\IRouterFactory;
use umi\route\type\factory\IRouteFactoryAware;
use umi\route\type\factory\TRouteFactoryAware;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для создания маршрутеризатора.
 */
class RouterFactory implements IRouterFactory, IRouteFactoryAware, IFactory
{

    use TRouteFactoryAware;
    use TFactory;

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
}