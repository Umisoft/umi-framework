<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\dispatcher\http;

use SplStack;
use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\IDispatcher;
use umi\http\request\Request;

/**
 * HTTP запрос компонента.
 */
class HTTPComponentRequest extends Request implements IHTTPComponentRequest
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
     * @var string $baseUrl базовый URL запроса к компоненту
     */
    protected $baseUrl = '';

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
    public function setRouteParams(array $params)
    {
        $this->getParams(self::ROUTE)
            ->setArray($params);

        return $this;
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

