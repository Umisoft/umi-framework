<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace umi\hmvc\context;

use umi\hmvc\component\IComponent;
use umi\hmvc\component\request\IComponentRequest;
use umi\route\result\IRouteResult;

/**
 * Контекст работы компонента.
 */
interface IContext
{
    /**
     * Возвращает HTTP запрос компонента из контекста.
     * @return IComponentRequest
     */
    public function getRequest();

    /**
     * Возвращает результат работы роутера из контекста.
     * @return IRouteResult
     */
    public function getRouteResult();

    /**
     * Возвращает компонент из контекста.
     * @return IComponent
     */
    public function getComponent();
}
