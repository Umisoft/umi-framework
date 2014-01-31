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
 * Контекст диспетчеризации MVC-компонентов.
 */
class DispatchContext implements IDispatchContext
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
     * @var string $baseUrl базовый URL запроса к компоненту
     */
    protected $baseUrl = '';
    /**
     * @var array $routeParams параметры маршрутизации
     */
    protected $routeParams = [];
    /**
     * @var SplStack $callStack стек вызова компонента
     */
    private $callStack;

    /**
     * Конструктор.
     * @param IComponent $component
     * @param IDispatcher $dispatcher диспетчер компонентов
     */
    public function __construct(IComponent $component, IDispatcher $dispatcher)
    {
        $this->component = $component;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setCallStack(SplStack $callStack)
    {
        $this->callStack = $callStack;

        return $this;
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
        if (!$this->callStack) {
            throw new RuntimeException(
                'Call stack is unknown.'
            );
        }
        return $this->callStack;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouteParams(array $params)
    {
        $this->routeParams = $params;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}
 