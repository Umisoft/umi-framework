<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\dispatcher;

use umi\hmvc\component\IComponent;
use umi\hmvc\component\response\IHTTPComponentResponse;
use umi\http\request\IRequest;

/**
 * Диспетчер MVC-компонентов.
 */
interface IDispatcher
{

    /**
     * Разделитель пути для вызова макроса
     */
    const MACROS_PATH_SEPARATOR = '.';

    /**
     * Обрабатывает http-запрос с помощью указанного MVC-компонента.
     * @param IComponent $component начальный компонент
     * @param IRequest $request
     * @return IHTTPComponentResponse
     */
    public function dispatchRequest(IComponent $component, IRequest $request);

    /**
     * Обрабатывает вызов макроса
     * @param IComponent $component начальный компонент
     * @param $macrosPath путь макроса
     * @param array $args аргументы вызова макроса
     * @return IHTTPComponentResponse
     */
    public function dispatchMacros(IComponent $component, $macrosPath, array $args = []);

}
 