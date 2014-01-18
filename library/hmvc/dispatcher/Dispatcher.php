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
use umi\hmvc\component\request\IHTTPComponentRequest;
use umi\hmvc\component\response\IHTTPComponentResponse;
use umi\hmvc\component\response\model\IDisplayModel;
use umi\hmvc\controller\IController;
use umi\hmvc\exception\http\HttpNotFound;
use umi\hmvc\exception\InvalidArgumentException;
use umi\hmvc\exception\RuntimeException;
use umi\hmvc\exception\UnexpectedValueException;
use umi\hmvc\IMVCEntityFactoryAware;
use umi\hmvc\macros\IMacros;
use umi\hmvc\TMVCEntityFactoryAware;
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
     * {@inheritdoc}
     */
    public function dispatchRequest(IComponent $component, IRequest $request)
    {
        $callStack = new SplStack();
        $callStack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_DELETE);

        $routePath = rtrim(parse_url($request->getRequestURI(), PHP_URL_PATH), '/');

        try {
            return $this->processRequest($component, $routePath, $callStack);
        } catch (Exception $e) {
            return $this->processError($e, $callStack);
        }

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
            return $this->processMacrosError($e, $callStack);
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
        return $this->callMacros($macros, $args);
    }

    /**
     * Формирует результат макроса с учетом произошедшей исключительной ситуации.
     * @param Exception $e
     * @param SplStack $callStack
     * @throws Exception если исключительная ситуация не была обработана
     * @return IHTTPComponentResponse
     */
    protected function processMacrosError(Exception $e, SplStack $callStack)
    {
        /**
         * @var IComponent $component
         */
        foreach ($callStack as $component) {
            if (!$component->hasMacros(IComponent::ERROR_MACROS)) {
                continue;
            }

            $errorMacros = $component->getMacros(IComponent::ERROR_MACROS);

            try {
                return $this->callMacros($errorMacros, [$e]);
            } catch (Exception $e) { }
        }

        throw $e;
    }

    /**
     * Вызывает макрос.
     * @param IMacros $macros
     * @param array $args
     * @throws UnexpectedValueException если макрос вернул неверный результат
     * @return IHTTPComponentResponse
     */
    protected function callMacros(IMacros $macros, array $args)
    {
        /** @noinspection PhpParamsInspection */
        $macrosResponse = call_user_func_array($macros, $args);

        if (!$macrosResponse instanceof IHTTPComponentResponse) {
            throw new UnexpectedValueException($this->translate(
                'Macros "{macros}" returns unexpected value. Instance of IHTTPComponentResponse expected.',
                ['macros' => get_class($macros)]
            ));
        }

        return $this->render($macrosResponse);
    }

    /**
     * Возвращает результат работы компонента.
     * @param IComponent $component
     * @param string $routePath запрос для маршрутизации
     * @param SplStack $callStack
     * @throws HttpNotFound если невозможно сформировать результат.
     * @return IHTTPComponentResponse
     */
    protected function processRequest(IComponent $component, $routePath, SplStack $callStack) {

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

            return $this->processRequest($childComponent, $routeResult->getUnmatchedUrl(), $callStack);

        } elseif (isset($routeMatches[IComponent::MATCH_CONTROLLER]) && !$routeResult->getUnmatchedUrl()) {
            if (!$component->hasController($routeMatches[IComponent::MATCH_CONTROLLER])) {
                throw new HttpNotFound(
                    $this->translate(
                        'Controller "{name}" not found.',
                        ['name' => $routeMatches[IComponent::MATCH_CONTROLLER]]
                    )
                );
            }

            $controller = $component->getController($routeMatches[IComponent::MATCH_CONTROLLER]);

            $componentRequest = $this->createMVCComponentRequest($component);
            $componentRequest->setRouteParams($routeMatches);

            $componentResponse = $this->callController($controller, $componentRequest);

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
     * Вызывает контроллер компонента.
     * @param IController $controller контроллер
     * @param IHTTPComponentRequest $componentRequest
     * @throws UnexpectedValueException если контроллер вернул неожиданный результат
     * @return IHTTPComponentResponse
     */
    protected function callController(IController $controller, IHTTPComponentRequest $componentRequest)
    {
        $componentResponse = $controller($componentRequest);

        if (!$componentResponse instanceof IHTTPComponentResponse) {
            throw new UnexpectedValueException($this->translate(
                'Controller "{controller}" returns unexpected value. Instance of IHTTPComponentResponse expected.',
                ['controller' => get_class($controller)]
            ));
        }

        return $this->render($componentResponse);
    }

    /**
     * Выполняет рендеринг результата при необходимости.
     * @param IHTTPComponentResponse $response
     * @throws RuntimeException
     * @return IHTTPComponentResponse
     */
    protected function render(IHTTPComponentResponse $response)
    {
        $content = $response->getContent();

        if ($content instanceof IDisplayModel) {
            $view = $response->getComponent()->getView();

            try {
                $content = $view->render($content->getTemplateName(), $content->getVariables());
            } catch (Exception $e) {
                throw new RuntimeException(
                    $this->translate(
                        'Cannot render template "{template}".',
                        [
                            'template' => $content->getTemplateName()
                        ]
                    ),
                    0,
                    $e
                );
            }

            $response->setContent($content);
        }

        return $response;
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
                $response = $this->callController($layoutController, $request);
            }
        }

        return $response;
    }

    /**
     * Формирует результат запроса с учетом произошедшей исключительной ситуации.
     * @param Exception $e произошедшая исключительная ситуация
     * @param SplStack $callStack
     * @throws Exception если не удалось обработать исключительную ситуацию
     * @return IHTTPComponentResponse
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

            $errorController = $component->getController(IComponent::ERROR_CONTROLLER, [$e]);
            $componentRequest = $this->createMVCComponentRequest($component);

            try {
                $errorResponse = $this->callController($errorController, $componentRequest);
                return $this->processResponse($componentRequest, $errorResponse, $callStack);
            } catch (Exception $e) { }
        }

        throw $e;
    }

}
 