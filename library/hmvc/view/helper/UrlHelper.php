<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view\helper;

use umi\hmvc\dispatcher\IDispatcher;
use umi\http\request\IRequest;

/**
 * Помощник вида для генерации URL по маршрутам компонента.
 */
class UrlHelper
{
    /**
     * @var IDispatcher $dispatcher
     */
    protected $dispatcher;

    /**
     * Конструктор.
     * @param IDispatcher $dispatcher диспетчер компонентов
     */
    public function __construct(IDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Формирует url для маршрута относительно HTTP-запроса к компоненту
     * @param string $routeName имя маршрута
     * @param array $params
     * @param bool $useQuery использовать ли GET-параметры текущего HTTP-запроса
     * @return string
     */
    public function __invoke($routeName, $params = [], $useQuery = false)
    {
        $context = $this->dispatcher->getCurrentContext();
        $baseUrl = $context->getBaseUrl();

        $url = $baseUrl . $context->getComponent()->getRouter()->assemble($routeName, $params) ? : '/';

        if ($useQuery) {
            $getParams = $this->dispatcher->getCurrentRequest()->getParams(IRequest::GET)->toArray();
            if($getParams) {
                $url .= '?' . http_build_query($getParams);
            }
        }

        return $url;
    }
}
