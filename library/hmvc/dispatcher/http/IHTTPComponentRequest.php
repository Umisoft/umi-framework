<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\dispatcher\http;

use umi\hmvc\dispatcher\IDispatchContext;
use umi\http\request\IRequest;

/**
 * Интерфейс HTTP запроса компонента.
 */
interface IHTTPComponentRequest extends IRequest, IDispatchContext
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
     * Устанавливает базовый URL запроса к компоненту.
     * @param string $baseUrl базовый URL запроса к компоненту
     * @return self
     */
    public function setBaseUrl($baseUrl);

    /**
     * Возвращает базовый URL запроса к компоненту.
     * @return string
     */
    public function getBaseUrl();

}

