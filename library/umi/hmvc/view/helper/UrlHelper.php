<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view\helper;

use umi\hmvc\component\request\IComponentRequest;
use umi\hmvc\context\IRequestContext;
use umi\hmvc\context\IRouterContext;
use umi\hmvc\context\TRequestContext;
use umi\hmvc\context\TRouterContext;

/**
 * Помощник вида для генерации URL по маршрутам компонента.
 */
class UrlHelper implements IRequestContext, IRouterContext
{
    use TRequestContext;
    use TRouterContext;

    /**
     * Возвращает маршрут.
     * @param string $name имя маршрута
     * @param array $params параметры
     * @param bool $useRequestParams использовать ли параметры из запроса
     * @return string
     */
    public function __invoke($name, array $params = [], $useRequestParams = false)
    {
        if ($useRequestParams) {
            $routeParams = $this->getContextRequest()
                ->getParams(IComponentRequest::ROUTE)
                ->toArray();

            $params += $routeParams;
        }

        return $this->getContextRouter()
            ->assemble($name, $params) ? : '/';
    }

}