<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view\helper;

use umi\hmvc\component\request\IHTTPComponentRequest;
use umi\hmvc\context\IContextAware;
use umi\hmvc\context\TContextAware;
use umi\hmvc\exception\RuntimeException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Помощник вида для генерации URL по маршрутам компонента.
 */
class UrlHelper implements IContextAware, ILocalizable
{
    use TContextAware;
    use TLocalizable;

    /**
     * Возвращает маршрут.
     * @param string $name имя маршрута
     * @param array $params параметры
     * @param bool $useRequestParams использовать ли параметры из запроса
     * @throws RuntimeException
     * @return string
     */
    public function __invoke($name, array $params = [], $useRequestParams = false)
    {
        return $name;

        if ($useRequestParams) {
            $request = $this->getContext()
                ->getRequest();

            if (!$request) {
                throw new RuntimeException($this->translate(
                    'Cannot get request from context.'
                ));
            }

            $routeParams = $request->getParams(IHTTPComponentRequest::ROUTE)->toArray();

            $params += $routeParams;
        }

        $router = $this->getContext()
            ->getComponent()->getRouter();

        return $router->assemble($name, $params) ? : '/';
    }
}
