<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\dispatcher;

use Exception;
use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\http\IHTTPComponentRequest;
use umi\hmvc\dispatcher\http\IHTTPComponentResponse;
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
     */
    public function dispatchRequest(IComponent $component, IRequest $request);

    /**
     * Сохраняет ошибку рендеринга результата работы контроллера.
     * @param IHTTPComponentRequest $request
     * @param Exception $e
     * @return self
     */
    public function reportControllerViewRenderError(IHTTPComponentRequest $request, Exception $e);

    /**
     * Формирует результат макроса с учетом произошедшей исключительной ситуации.
     * @param IDispatchContext $macrosRequest контекст вызова макроса
     * @param Exception $e
     * @throws Exception если исключительная ситуация не была обработана
     * @return string
     */
    public function processMacrosError(IDispatchContext $macrosRequest, Exception $e);

    /**
     * Обрабатывает вызов макроса
     * @param IComponent $component начальный компонент
     * @param $macrosPath путь макроса
     * @param array $args аргументы вызова макроса
     * @return IHTTPComponentResponse
     */
    public function dispatchMacros(IComponent $component, $macrosPath, array $args = []);

}
 