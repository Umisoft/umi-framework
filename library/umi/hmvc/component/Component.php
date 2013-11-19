<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component;

use umi\hmvc\component\request\IComponentRequest;
use umi\hmvc\component\request\IComponentRequestAware;
use umi\hmvc\component\request\TComponentRequestAware;
use umi\hmvc\component\response\IComponentResponse;
use umi\hmvc\component\response\IComponentResponseAware;
use umi\hmvc\component\response\TComponentResponseAware;
use umi\hmvc\context\Context;
use umi\hmvc\context\IContext;
use umi\hmvc\context\IContextAware;
use umi\hmvc\controller\IControllerFactory;
use umi\hmvc\component\response\model\IDisplayModel;
use umi\hmvc\exception\http\HttpNotFound;
use umi\hmvc\exception\OutOfBoundsException;
use umi\hmvc\IMVCLayerAware;
use umi\hmvc\model\IModelAware;
use umi\hmvc\model\IModelFactory;
use umi\hmvc\TMVCLayerAware;
use umi\hmvc\view\IView;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\route\IRouteAware;
use umi\route\IRouter;
use umi\route\result\IRouteResult;
use umi\route\TRouteAware;

/**
 * Реализация MVC компонента системы.
 */
class Component implements IComponent, IMVCLayerAware, IComponentAware, IRouteAware, IComponentRequestAware, IComponentResponseAware, ILocalizable
{
    use TMVCLayerAware;
    use TComponentAware;
    use TRouteAware;
    use TComponentRequestAware;
    use TComponentResponseAware;
    use TLocalizable;

    /**
     * @var array $options опции компонента
     */
    private $options;
    /**
     * @var IRouter $router роутер компонента
     */
    private $router;
    /**
     * @var IControllerFactory $controllerFactory фабрика контроллеров
     */
    private $controllerFactory;
    /**
     * @var IModelFactory $modelFactory фабрика моделей
     */
    private $modelFactory;
    /**
     * @var IView $view слой отображения
     */
    private $view;

    /**
     * Конструктор.
     * @param array $options опции
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildComponent($name)
    {
        if (!$this->hasChildComponent($name)) {
            throw new OutOfBoundsException($this->translate(
                'Cannot create child component "{name}". Component has not registered.',
                ['name' => $name]
            ));
        }

        return $this->createHMVCComponent($this->options[self::OPTION_COMPONENTS][$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouter()
    {
        if (!$this->router) {
            $config = isset($this->options[self::OPTION_ROUTES]) ? $this->options[self::OPTION_ROUTES] : [];

            return $this->router = $this->createRouter($config);
        }

        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(IComponentRequest $request)
    {
        $this->processRequest($request);

        $context = new Context(
            $this,
            $request,
            $this->route($request)
        );

        $response = $this->dispatch($context);

        if ($response->isProcessable()) {
            $this->processResponse($response, $request);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function call($name, IComponentRequest $request)
    {
        $context = new Context($this, $request);

        return $this->callController(
            $name,
            $context
        );
    }

    /**
     * Выполняет маршрутизацию запроса.
     * @param IComponentRequest $request HTTP запрос
     * @return IRouteResult результат маршрутизации
     */
    protected function route(IComponentRequest $request)
    {
        $result = $this->getRouter()
            ->match($request->getRequestUri());

        $request->setRouteParams($result->getMatches());

        return $result;
    }

    /**
     * Выполняет отправку запроса на выполнение в контроллер, либо в дочерний компонент.
     *
     * @param IContext $context контекст работы компонента
     * @return IComponentResponse результат работы компонента
     */
    protected function dispatch(IContext $context)
    {
        $matches = $context->getRouteResult()->getMatches();

        if (isset($matches[self::MATCH_COMPONENT])) {
            return $this->callChildComponent($matches[self::MATCH_COMPONENT], $context);
        } elseif (isset($matches[self::MATCH_CONTROLLER]) && !$context->getRouteResult()->getUnmatchedUrl()) {
            return $this->callController($matches[self::MATCH_CONTROLLER], $context);
        } else {
            return $this->callErrorController(
                new HttpNotFound($this->translate(
                    'URL not found by router.'
                )),
                $context
            );
        }
    }

    /**
     * Вызывает дочерний компонент.
     * @param string $component компонент
     * @param IContext $context контекст родительского компонента
     * @return IComponentResponse
     */
    protected function callChildComponent($component, IContext $context)
    {
        if (!$this->hasChildComponent($component)) {
            return $this->callErrorController(
                new HttpNotFound($this->translate(
                    'Child component "{name}" not found.',
                    ['name' => $component]
                )),
                $context
            );
        }

        $childComponent = $this->getChildComponent($component);

        /**
         * @var IRouter $router
         */
        $router = $childComponent->getRouter();
        $router->setBaseUrl($context->getRouteResult()->getMatchedUrl());

        $componentRequest = $this->createComponentRequest($context->getRouteResult()->getUnmatchedUrl());

        try {
            return $childComponent->execute($componentRequest);
        } catch (\Exception $e) {
            return $this->callErrorController($e, $context);
        }
    }

    /**
     * Вызывает контроллер компонента.
     * @param string $controller имя контроллера
     * @param IContext $context контекст вызова контроллера
     * @return IComponentResponse
     */
    protected function callController($controller, IContext $context)
    {
        $controller = $this->getControllerFactory()
            ->createController($controller);

        try {
            $result = $controller($context->getRequest());

            return $this->render($result, $context);
        } catch (\Exception $exception) {
            return $this->callErrorController($exception, $context);
        }
    }

    /**
     * Проверяет, существует ли дочерний компонент с заданным именем.
     * @param string $name имя компонента
     * @return bool
     */
    protected function hasChildComponent($name)
    {
        return isset($this->options[self::OPTION_COMPONENTS][$name]);
    }

    /**
     * Обрабатывает результат работы дочернего компонента.
     * @param IComponentResponse $response результат работы компонента
     * @param IComponentRequest $request запрос компонента
     */
    protected function processResponse(IComponentResponse &$response, IComponentRequest $request)
    {
    }

    /**
     * Обрабатывает запрос компонента.
     * @param IComponentRequest $request запрос к компоненту
     */
    protected function processRequest(IComponentRequest &$request)
    {
    }

    /**
     * Возвращает фабрику контроллеров компонента.
     * @return IControllerFactory
     */
    protected function getControllerFactory()
    {
        if (!$this->controllerFactory) {
            $config = isset($this->options[self::OPTION_CONTROLLERS]) ? $this->options[self::OPTION_CONTROLLERS] : [];
            $controllerFactory = $this->createMvcControllerFactory($config);

            if ($controllerFactory instanceof IModelAware) {
                $controllerFactory->setModelFactory($this->getModelsFactory());
            }

            return $this->controllerFactory = $controllerFactory;
        }

        return $this->controllerFactory;
    }

    /**
     * Возвращает фабрику моделей компонента.
     * @return IModelFactory
     */
    protected function getModelsFactory()
    {
        if (!$this->modelFactory) {
            $config = isset($this->options[self::OPTION_MODELS]) ? $this->options[self::OPTION_MODELS] : [];

            return $this->modelFactory = $this->createMvcModelFactory($config);
        }

        return $this->modelFactory;
    }

    /**
     * Возвращает слой отображения для компонента.
     * @return IView
     */
    protected function getView()
    {
        if (!$this->view) {
            $config = isset($this->options[self::OPTION_VIEW]) ? $this->options[self::OPTION_VIEW] : [];

            $view = $this->createMvcView($config);

            if ($view instanceof IModelAware) {
                $view->setModelFactory($this->getModelsFactory());
            }

            return $this->view = $view;
        }

        return $this->view;
    }

    /**
     * Вызывает error controller компонента если зарегистрирован.
     * @param \Exception $exception исключение для обработки error controller
     * @param IContext $context контекст вызова
     * @throws \Exception если error controller не зарегистрирован
     * @return IComponentResponse
     */
    protected function callErrorController(\Exception $exception, IContext $context)
    {
        if (!$this->getControllerFactory()
            ->hasController(self::ERROR_CONTROLLER)
        ) {
            throw $exception;
        }

        $controller = $this->getControllerFactory()
            ->createController(self::ERROR_CONTROLLER, [$exception]);

        $result = $controller($context->getRequest());

        return $this->render($result, $context);
    }

    /**
     * Выполняет рендеринг результата при необходимости.
     * @param IComponentResponse $response
     * @param IContext $context контекст работы компонента
     * @return IComponentResponse
     */
    private function render(IComponentResponse $response, IContext $context)
    {
        $content = $response->getContent();

        if ($content instanceof IDisplayModel) {
            $view = $this->getView();

            if ($view instanceof IContextAware) {
                $view->setContext($context);
            }

            $response->setContent($view->render($content->getTemplate(), $content->getVariables()));

            if ($view instanceof IContextAware) {
                $view->clearContext();
            }
        }

        return $response;
    }
}
