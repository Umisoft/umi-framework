<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\type\factory;

use umi\route\type\IRoute;

/**
 * Интерфейс фабрики правил маршрутеризации.
 */
interface IRouteFactory
{
    /**
     * Тип правила маршрутизатора - фиксированный(статический) маршрут.
     * @example /user/register
     */
    const ROUTE_FIXED = 'fixed';
    /**
     * Тип правил маршрутизатора, основанный на регулярных выражениях.
     * @example /user/(?P<id>(\d+))
     */
    const ROUTE_REGEXP = 'regexp';
    /**
     * Тип правила маршрутизатора - простой маршрут.
     * @example /{controller:string}/{action:integer}
     */
    const ROUTE_SIMPLE = 'simple';
    /**
     * Тип правила маршрутизатора - простой расширеный маршрут.
     * @example /{controller}/{action}
     */
    const ROUTE_EXTENDED = 'extended';

    /**
     * Возвращает правила маршрутизатора на основе массива конфигурации.
     * @param array $config конфигурация
     * @return IRoute[] массив правил маршрутизатора
     */
    public function createRoutes(array $config);
}