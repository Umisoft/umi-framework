<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route;

/**
 * Интерфейс фабрики правил маршрутеризации.
 */
interface IRouteFactory
{
    /** Опция для установки типа создаваемого маршрута */
    const OPTION_TYPE = 'type';
    /** Опция для установки дочерних маршрутов */
    const OPTION_SUBROUTES = 'subroutes';

    /** Тип правила маршрутизатора - фиксированный(статический) маршрут. */
    const ROUTE_FIXED = 'fixed';
    /** Тип правил маршрутизатора, основанный на регулярных выражениях. */
    const ROUTE_REGEXP = 'regexp';
    /** Тип правила маршрутизатора - простой маршрут. */
    const ROUTE_SIMPLE = 'simple';
    /** Тип правила маршрутизатора - простой расширеный маршрут. */
    const ROUTE_EXTENDED = 'extended';

    /**
     * Создает маршрутеризатор на основе конфигурации.
     * @param array $config конфигурация
     * @return IRouter
     */
    public function createRouter(array $config);
}