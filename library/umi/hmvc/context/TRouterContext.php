<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\context;

use umi\hmvc\exception\RequiredDependencyException;
use umi\route\IRouter;

/**
 * Трейт для поддержки внедрения маршрутизатора из контекста.
 */
trait TRouterContext
{
    /**
     * @var IRouter $_contextRouter маршрутизатор
     */
    private $_contextRouter;

    /**
     * Устанавливает контекстно-зависимый маршрутизатор.
     * @param IRouter $router маршрутизатор
     */
    public function setContextRouter(IRouter $router = null)
    {
        $this->_contextRouter = $router;
    }

    /**
     * Проверяет доступность контекстно зависимого маршрутеризатора.
     * @return bool
     */
    protected function hasContextRouter()
    {
        return !is_null($this->_contextRouter);
    }

    /**
     * Возвращает контекстно-зависимый маршрутизатор.
     * @return IRouter
     * @throws RequiredDependencyException если контекст не установлен
     */
    protected function getContextRouter()
    {
        if (!$this->_contextRouter) {
            throw new RequiredDependencyException(sprintf(
                'Context router has not injected.'
            ));
        }

        return $this->_contextRouter;
    }
}
 