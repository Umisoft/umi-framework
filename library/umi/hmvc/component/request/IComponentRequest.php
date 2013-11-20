<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component\request;

use umi\http\request\IRequest;

/**
 * Интерфейс HTTP запроса компонента.
 */
interface IComponentRequest extends IRequest
{
    /** HTTP контейнер - ROUTE */
    const ROUTE = 'route';

    /**
     * Устанавливает параметры маршрутизации для HTTP запроса компонента.
     * @param array $params параметры маршрутизации
     * @return self
     */
    public function setRouteParams(array $params);
}
