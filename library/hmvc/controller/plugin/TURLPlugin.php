<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller\plugin;

use umi\hmvc\dispatcher\http\IHTTPComponentRequest;
use umi\hmvc\context\IContext;

/**
 * Помощник контроллера для генерации URL.
 */
trait TURLPlugin
{
    /**
     * Возвращает контекст текущего компонента.
     * @return IContext
     */
    abstract protected function getContext();

    /**
     * Возвращает URL, созданный маршрутизатором текущего компонента.
     * @param string $route имя маршрута
     * @param array $parameters параметры маршрута
     * @param bool $useRequest использовать ли параметры из запроса
     * @return string
     */
    protected function getUrl($route, array $parameters = [], $useRequest = false)
    {
        $router = $this->getContext()->getComponent()->getRouter();

        if ($useRequest) {
            $request = $this->getContext()->getRequest();

            $parameters += $request->getParams(IHTTPComponentRequest::ROUTE)->toArray();
        }

        return $router->assemble($route, $parameters) ?: '/';
    }

    /**
     * Возвращает абсолютный URL, созданный маршрутизатором текущего компонента.
     * @param string $route имя маршрута
     * @param array $parameters параметры маршрута
     * @param bool $useRequest использовать ли параметры из запроса
     * @return string
     */
    protected function getAbsoluteUrl($route, array $parameters = [], $useRequest = false)
    {
        $request = $this->getContext()->getRequest();

        return $request->getScheme() . '://' . $request->getHost() . $this->getUrl($route, $parameters, $useRequest);
    }
}
