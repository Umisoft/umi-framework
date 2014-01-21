<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace umi\hmvc\context;

use umi\route\result\IRouteResult;
use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\http\IHTTPComponentRequest;

/**
 * Контекст работы компонента.
 */
class Context implements IContext
{
    /**
     * @var IHTTPComponentRequest $request
     */
    private $request;
    /**
     * @var IRouteResult $routeResult
     */
    private $routeResult;
    /**
     * @var IComponent $component
     */
    private $component;

    /**
     * Конструктор.
     * @param IComponent $component
     * @param IRouteResult $routeResult
     * @param IHTTPComponentRequest $request
     */
    public function __construct(IComponent $component, IHTTPComponentRequest $request, IRouteResult $routeResult = null)
    {
        $this->component = $component;
        $this->routeResult = $routeResult;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteResult()
    {
        return $this->routeResult;
    }

    /**
     * {@inheritdoc}
     */
    public function getComponent()
    {
        return $this->component;
    }
}
