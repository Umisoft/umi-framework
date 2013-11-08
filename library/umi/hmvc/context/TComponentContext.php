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
use umi\hmvc\exception\RequiredDependencyException;

/**
 * Трейт для поддержки внедрения компонента из контекста.
 */
trait TComponentContext
{
    /**
     * @var IComponent $_contextComponent компонент
     */
    private $_contextComponent;

    /**
     * Устанавливает контекстно-зависимый компонент.
     * @param IComponent $component компонент
     */
    public function setContextComponent(IComponent $component = null)
    {
        $this->_contextComponent = $component;
    }

    /**
     * Проверяет доступность контекстно зависимого компонента.
     * @return bool
     */
    protected function hasContextComponent()
    {
        return !is_null($this->_contextComponent);
    }

    /**
     * Возвращает компонент в текущем контексте.
     * @return IComponent
     * @throws RequiredDependencyException если контекст не был внедрен
     */
    protected function getContextComponent()
    {
        if (!$this->_contextComponent) {
            throw new RequiredDependencyException(sprintf(
                'Context component has not injected.'
            ));
        }

        return $this->_contextComponent;
    }
}
 