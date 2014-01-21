<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\dispatcher\macros;

use SplStack;
use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\IDispatchContext;
use umi\hmvc\dispatcher\IDispatcher;

/**
 * Контекст вызова макроса
 */
class MacrosRequest implements IDispatchContext
{
    /**
     * @var IComponent $component
     */
    protected $component;
    /**
     * @var IDispatcher $dispatcher диспетчер компонентов
     */
    protected $dispatcher;
    /**
     * @var SplStack $callStack стек вызова компонентов
     */
    protected $callStack;

    /**
     * Конструктор.
     * @param IComponent $component
     * @param IDispatcher $dispatcher диспетчер компонентов
     * @param SplStack $callStack стек вызова компонентов
     */
    public function __construct(IComponent $component, IDispatcher $dispatcher, SplStack $callStack)
    {
        $this->component = $component;
        $this->dispatcher = $dispatcher;
        $this->callStack = $callStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * {@inheritdoc}
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallStack()
    {
        return $this->callStack;
    }

}

 