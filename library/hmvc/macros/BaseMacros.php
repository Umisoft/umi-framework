<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\macros;

use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\IDispatchContext;
use umi\hmvc\exception\RequiredDependencyException;
use umi\hmvc\view\IView;
use umi\hmvc\view\View;

/**
 * Базовая реализация макроса компонента.
 */
abstract class BaseMacros implements IMacros
{
    /**
     * @var IDispatchContext $context контекст вызова макроса
     */
    private $context;

    /**
     * {@inheritdoc}
     */
    public function setContext(IDispatchContext $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Возвращает контекст вызова макроса.
     * @throws RequiredDependencyException если контекст не был установлен
     * @return IDispatchContext
     */
    protected function getContext()
    {
        if (!$this->context) {
            throw new RequiredDependencyException(
                sprintf('Context is not injected in macros "%s".', get_class($this))
            );
        }

        return $this->context;
    }

    /**
     * Возвращает компонент, которому принадлежит контроллер.
     * @throws RequiredDependencyException если контроллер не был установлен
     * @return IComponent
     */
    protected function getComponent()
    {
        return $this->getContext()->getComponent();
    }

    /**
     * Создает результат работы макроса, требующий шаблонизации.
     * @param string $templateName имя шаблона
     * @param array $variables переменные
     * @return IView
     */
    protected function createResult($templateName, array $variables)
    {
        return new View($this, $this->getContext(), $templateName, $variables);
    }


}

 