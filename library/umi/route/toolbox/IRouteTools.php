<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\toolbox;

use umi\route\IRouterFactory;
use umi\toolkit\toolbox\IToolbox;

/**
 * Набор инструментов маршрутизации.
 */
interface IRouteTools extends IToolbox
{
    /**
     * Короткий alias
     */
    const ALIAS = 'route';

    /**
     * Возвращает фабрику для создания маршрутизатора.
     * @return IRouterFactory
     */
    public function getRouteFactory();

}