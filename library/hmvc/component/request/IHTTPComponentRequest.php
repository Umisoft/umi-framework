<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component\request;

use umi\hmvc\component\IComponent;
use umi\http\request\IRequest;

/**
 * Интерфейс HTTP запроса компонента.
 */
interface IHTTPComponentRequest extends IRequest
{
    /**
     * HTTP контейнер - ROUTE
     */
    const ROUTE = 'route';

    /**
     * Устанавливает параметры маршрута, соответствующего HTTP запросу к компоненту.
     * @param array $params параметры маршрута
     * @return self
     */
    public function setRouteParams(array $params);

    /**
     * Возвращает компонент.
     * @return IComponent
     */
    public function getComponent();
}
