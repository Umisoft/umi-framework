<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\dispatcher;

use SplStack;
use umi\hmvc\component\IComponent;
use umi\hmvc\exception\RuntimeException;

/**
 * Интерфейс контекста диспетчеризации MVC-компонентов.
 */
interface IDispatchContext
{
    /**
     * Устанавливает стек вызова компонента.
     * @param SplStack $callStack
     * @return self
     */
    public function setCallStack(SplStack $callStack);

    /**
     * Возвращает компонент контекста.
     * @return IComponent
     */
    public function getComponent();

    /**
     * Возвращает диспетчер компонентов.
     * @return IDispatcher
     */
    public function getDispatcher();

    /**
     * Возвращает стек вызова компонента.
     * @throws RuntimeException если стек не был установлен.
     * @return SplStack
     */
    public function getCallStack();

    /**
     * Устанавливает параметры маршрута, соответствующего HTTP запросу к компоненту.
     * @param array $params параметры маршрута
     * @return self
     */
    public function setRouteParams(array $params);

    /**
     * Возвращает параметры маршрута, соответствующего HTTP запросу к компоненту.
     * @return array
     */
    public function getRouteParams();

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
 