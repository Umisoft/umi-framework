<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\dispatcher;

use Exception;
use SplDoublyLinkedList;
use SplStack;
use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\http\HTTPComponentRequest;
use umi\hmvc\dispatcher\http\IHTTPComponentRequest;
use umi\hmvc\dispatcher\http\IHTTPComponentResponse;
use umi\hmvc\controller\IController;
use umi\hmvc\dispatcher\macros\MacrosRequest;
use umi\hmvc\exception\http\HttpNotFound;
use umi\hmvc\exception\InvalidArgumentException;
use umi\hmvc\exception\UnexpectedValueException;
use umi\hmvc\IMVCEntityFactoryAware;
use umi\hmvc\macros\IMacros;
use umi\hmvc\TMVCEntityFactoryAware;
use umi\hmvc\view\IView;
use umi\http\request\IRequest;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Диспетчер MVC-компонентов.
 */
class Dispatcher implements IDispatcher, ILocalizable, IMVCEntityFactoryAware
{

    use TLocalizable;
    use TMVCEntityFactoryAware;

    /**
     * @var array $renderError информация об ошибке рендеринга
     */
    protected $controllerViewRenderErrorInfo = [];

    /**
     * {@inheritdoc}
     */
    public function dispatchRequest(IComponent $component, IRequest $request)
    {
        $callStack = new SplStack();
        $callStack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_DELETE);

        $routePath = rtrim(parse_url($request->getRequestURI(), PHP_URL_PATH), '/');

        try {
            $response = $this->processRequest($component, $routePath, $callStack);
        } catch (Exception $e) {
            $this->processError($e, $callStack);
            return;
        }

        $content = (string) $response->getContent();

        if ($this->controllerViewRenderErrorInfo) {
            /**
             * @var IHTTPComponentRequest $failureRequest
             * @var Exception $e
             */
            list ($failureRequest, $e) = $this->controllerViewRenderErrorInfo;
            $this->controllerViewRenderErrorInfo = [];

            $this->processError($e, $failureRequest->getCallStack());
            return;
        }

        $response->setContent($content);
        $response->send();
    }

    /**
     * {@inheritdoc}
     */
    public function reportControllerViewRenderError(IHTTPComponentRequest $request, Exception $e)
    {
        $this->controllerViewRenderErrorInfo = [$request, $e];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchMacros(IComponent $component, $macrosPath, array $args = [])
    {
        $callStack = new SplStack();
        $callStack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_DELETE);

        try {
            return $this->processMacros($component, $macrosPath, $args, $callStack);
        } catch (Exception $e) {
            return $this->processMacrosError($this->createMacrosRequest($component, clone $callStack), $e);
        }
    }

    /**
     * Возвращает результат работы макроса.
     * @param IComponent $component начальный компонент поиска
     * @param string $macrosPath путь к макросу
     * @param array $args
     * @param SplStack $callStack
     * @throws InvalidArgumentException если задан неверный путь макроса
     * @return IHTTPComponentResponse
     */
    protected function processMacros(IComponent $component, $macrosPath, array $args, SplStack $callStack)
    {
        $callStack->push($component);

        $macrosPathInfo = explode(self::MACROS_PATH_SEPARATOR, $macrosPath);

        if (!$macrosPathInfo) {
            throw new InvalidArgumentException(
                $this->translate(
                    'Invalid macros path "{path}".',
                    ['path' => $macrosPath]
                )
            );
        }

        $macrosName = array_pop($macrosPathInfo);

        while ($childComponentName = array_shift($macrosPathInfo)) {
            $component = $component->getChildComponent($childComponentName);
            $callStack->push($component);
        }

        $macros = $component->getMacros($macrosName);
        $macros->setMacrosRequest($this->createMacrosRequest($component, clone $callStack));

        return $this->callMacros($macros, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function processMacrosError(IDispatchContext $macrosRequest, Exception $e)
    {
        $callStack = $macrosRequest->getCallStack();
        /**
         * @var IComponent $component
         */
        foreach ($callStack as $component) {
            if (!$component->hasMacros(IComponent::ERROR_MACROS)) {
                continue;
            }

            $errorMacros = $component->getMacros(IComponent::ERROR_MACROS);
            $errorMacros->setMacrosRequest($this->createMacrosRequest($component, clone $callStack));

            try {
                return (string) $this->callMacros($errorMacros, [$e]);
            } catch (Exception $e) { }
        }

        return $e->getMessage();
    }

    /**
     * Вызывает макрос.
     * @param IMacros $macros
     * @param array $args
     * @throws UnexpectedValueException если макрос вернул неверный результат
     * @return IView|string
     */
    protected function callMacros(IMacros $macros, array $args)
    {
        /** @noinspection PhpParamsInspection */
        $macrosResult = call_user_func_array($macros, $args);

        if (!$macrosResult instanceof IView && !is_string($macrosResult)) {
            throw new UnexpectedValueException($this->translate(
                'Macros "{macros}" returns unexpected value. String or instance of IView expected.',
                ['macros' => get_class($macros)]
            ));
        }

        return $macrosResult;
    }

    /**
     * Возвращает результат работы компонента.
     * @param IComponent $component
     * @param string $routePath запрос для маршрутизации
     * @param SplStack $callStack
     * @param string $matchedRoutePath обработанная часть начального маршрута
     * @throws HttpNotFound если невозможно сформировать результат.
     * @return IHTTPComponentResponse
     */
    protected function processRequest(IComponent $component, $routePath, SplStack $callStack, $matchedRoutePath = '') {

        $callStack->push($component);

        $routeResult = $component->getRouter()->match($routePath);
        $routeMatches = $routeResult->getMatches();

        if (isset($routeMatches[IComponent::MATCH_COMPONENT])) {

            if (!$component->hasChildComponent($routeMatches[IComponent::MATCH_COMPONENT])) {

                throw new HttpNotFound(
                    $this->translate(
                        'Child component "{name}" not found.',
                        ['name' => $routeMatches[IComponent::MATCH_COMPONENT]]
                    )
                );
            }

            $childComponent = $component->getChildComponent($routeMatches[IComponent::MATCH_COMPONENT]);

            $matchedRoutePath .= $routeResult->getMatchedUrl();
            return $this->processRequest($childComponent, $routeResult->getUnmatchedUrl(), $callStack, $matchedRoutePath);

        } elseif (isset($routeMatches[IComponent::MATCH_CONTROLLER]) && !$routeResult->getUnmatchedUrl()) {
            if (!$component->hasController($routeMatches[IComponent::MATCH_CONTROLLER])) {
                throw new HttpNotFound(
                    $this->translate(
                        'Controller "{name}" not found.',
                        ['name' => $routeMatches[IComponent::MATCH_CONTROLLER]]
                    )
                );
            }

            $componentRequest = $this->createComponentRequest($component, clone $callStack)
                ->setRouteParams($routeMatches)
                ->setBaseUrl($matchedRoutePath);

            $controller = $component->getController($routeMatches[IComponent::MATCH_CONTROLLER]);
            $controller->setHTTPComponentRequest($componentRequest);

            $componentResponse = $this->callController($controller);

            return $this->processResponse($componentRequest, $componentResponse, $callStack);


        } else {
            throw new HttpNotFound(
                $this->translate(
                    'URL not found by router.'
                )
            );
        }
    }

    /**
     * Формирует результат запроса с учетом произошедшей исключительной ситуации.
     * @param Exception $e произошедшая исключительная ситуация
     * @param SplStack $callStack
     * @throws Exception если не удалось обработать исключительную ситуацию
     */
    protected function processError(Exception $e, SplStack $callStack)
    {
        /**
         * @var IComponent $component
         */
        foreach ($callStack as $component) {
            if (!$component->hasController(IComponent::ERROR_CONTROLLER)) {
                continue;
            }

            $errorController = $component->getController(IComponent::ERROR_CONTROLLER);
            $componentRequest = $this->createComponentRequest($component, clone $callStack);
            $errorController->setHTTPComponentRequest($componentRequest);

            try {
                $errorResponse = $this->callController($errorController, [$e]);
                $layoutResponse = $this->processResponse($componentRequest, $errorResponse, $callStack);
            } catch (Exception $e) {
                continue;
            }
            $content = (string) $layoutResponse->getContent();
            if ($this->controllerViewRenderErrorInfo) {
                list (, $renderException) = $this->controllerViewRenderErrorInfo;
                throw $renderException;
            }

            $layoutResponse
                ->setContent($content)
                ->send();

            return;
        }

        throw $e;
    }

    /**
     * Вызывает контроллер компонента.
     * @param IController $controller контроллер
     * @param array $args параметры вызова контроллера
     * @throws UnexpectedValueException если контроллер вернул неожиданный результат
     * @return IHTTPComponentResponse
     */
    protected function callController(IController $controller, array $args = [])
    {
        /** @noinspection PhpParamsInspection */
        $componentResponse = call_user_func_array($controller, $args);

        if (!$componentResponse instanceof IHTTPComponentResponse) {
            throw new UnexpectedValueException($this->translate(
                'Controller "{controller}" returns unexpected value. Instance of IHTTPComponentResponse expected.',
                ['controller' => get_class($controller)]
            ));
        }

        return $componentResponse;
    }

    /**
     * Обрабатывает результат запроса по всему стеку вызова компонентов.
     * @param IHTTPComponentRequest $request
     * @param IHTTPComponentResponse $response
     * @param SplStack $callStack
     * @return IHTTPComponentResponse
     */
    protected function processResponse(IHTTPComponentRequest $request, IHTTPComponentResponse $response, SplStack $callStack)
    {
        /**
         * @var IComponent $component
         */
        foreach ($callStack as $component) {
            if ($response->isProcessable()) {
                if (!$component->hasController(IComponent::LAYOUT_CONTROLLER)) {
                    continue;
                }

                $layoutController = $component->getController(IComponent::LAYOUT_CONTROLLER, [$response]);
                $layoutController->setHTTPComponentRequest($request);
                $response = $this->callController($layoutController);
            }
        }

        return $response;
    }

    /**
     * Создает контекст http-вызова компонента
     * @param IComponent $component
     * @param SplStack $callStack
     * @return IHTTPComponentRequest
     */
    protected function createComponentRequest(IComponent $component, SplStack $callStack)
    {
        return new HTTPComponentRequest($component, $this, $callStack);
    }

    /**
     * Создает контекст вызова макроса
     * @param IComponent $component
     * @param SplStack $callStack
     * @return IHTTPComponentRequest
     */
    protected function createMacrosRequest(IComponent $component, SplStack $callStack)
    {
        return new MacrosRequest($component, $this, $callStack);
    }

}
 