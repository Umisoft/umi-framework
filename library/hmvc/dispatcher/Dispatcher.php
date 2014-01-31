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
use umi\authentication\IAuthenticationAware;
use umi\authentication\TAuthenticationAware;
use umi\hmvc\acl\IACLResource;
use umi\hmvc\acl\IACLRoleProvider;
use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\http\IHTTPComponentResponse;
use umi\hmvc\controller\IController;
use umi\hmvc\exception\http\HttpForbidden;
use umi\hmvc\exception\http\HttpNotFound;
use umi\hmvc\exception\RuntimeException;
use umi\hmvc\exception\UnexpectedValueException;
use umi\hmvc\IMVCEntityFactoryAware;
use umi\hmvc\macros\IMacros;
use umi\hmvc\TMVCEntityFactoryAware;
use umi\hmvc\view\IView;
use umi\http\request\IRequest;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\route\result\IRouteResult;

/**
 * Диспетчер MVC-компонентов.
 */
class Dispatcher implements IDispatcher, ILocalizable, IMVCEntityFactoryAware, IAuthenticationAware
{

    use TLocalizable;
    use TMVCEntityFactoryAware;
    use TAuthenticationAware;

    /**
     * @var array $controllerViewRenderErrorInfo информация об исключение рендеринга
     */
    protected $controllerViewRenderErrorInfo = [];
    /**
     * @var IRequest $currentRequest обрабатываемый HTTP-запрос
     */
    protected $currentRequest;
    /**
     * @var IComponent $initialComponent начальный компонент HTTP-запроса
     */
    protected $initialComponent;

    /**
     * @var IDispatchContext $currentContext текущий контекст
     */
    private $currentContext;

    /**
     * {@inheritdoc}
     */
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchRequest(IComponent $component, IRequest $request)
    {
        $this->currentRequest = $request;
        $this->initialComponent = $component;

        $callStack = $this->createCallStack();

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
             * @var Exception $e
             * @var IDispatchContext $failureContext
             */
            list ($e, $failureContext) = $this->controllerViewRenderErrorInfo;
            $this->controllerViewRenderErrorInfo = [];

            $this->processError($e, $failureContext->getCallStack());
            return;
        }

        $response->setContent($content);
        $response->send();
    }

    /**
     * {@inheritdoc}
     */
    public function reportViewRenderError(Exception $e, IDispatchContext $failureContext, $viewOwner)
    {
        if ($viewOwner instanceof IMacros) {
            return $this->processMacrosError($e, $failureContext);
        }

        $this->controllerViewRenderErrorInfo = [$e, $failureContext];

        return $e->getMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function executeMacros($macrosURI, array $params = [])
    {

        list ($component, $callStack, $componentURI) = $this->resolveMacrosContext($macrosURI);

        try {
            $macros = $this->dispatchMacros($component, $macrosURI, $params, $callStack, $componentURI);

            return $this->invokeMacros($macros);

        } catch (Exception $e) {

            $context = $this->createDispatchContext($component);
            $context->setCallStack(clone $callStack);

            return $this->processMacrosError($e, $context);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function switchCurrentContext(IDispatchContext $context)
    {
        $previousContext = $this->currentContext;
        $this->currentContext = $context;

        return $previousContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentContext()
    {
        if (!$this->currentContext) {
            throw new RuntimeException(
                'Current dispatch context is unknown.'
            );
        }

        return $this->currentContext;
    }

    /**
     * Формирует результат макроса с учетом произошедшей исключительной ситуации.
     * @param Exception $e
     * @param IDispatchContext $context контекст вызова макроса
     * @throws Exception если исключительная ситуация не была обработана
     * @return string
     */
    protected function processMacrosError(Exception $e, IDispatchContext $context)
    {
        $callStack = $context->getCallStack();
        /**
         * @var IDispatchContext $context
         */
        foreach ($callStack as $context) {

            $component = $context->getComponent();
            if (!$component->hasMacros(IComponent::ERROR_MACROS)) {
                continue;
            }

            $errorMacros = $component->getMacros(
                IComponent::ERROR_MACROS,
                ['exception' => $e]
            );

            $context = $this->createDispatchContext($component);
            $context->setCallStack(clone $callStack);

            $errorMacros->setContext($context);

            try {
                return (string) $this->invokeMacros($errorMacros);
            } catch (Exception $e) { }
        }

        return $e->getMessage();
    }

    /**
     * Диспетчеризирует вызов макроса.
     * @param IComponent $component компонент для поиска
     * @param string $macrosURI путь макроса относительно компонента
     * @param array $params параметры вызова макроса
     * @param SplStack $callStack стек вызова компонентов
     * @param string $matchedMacrosURI известная часть пути вызова макроса
     * @return IMacros
     */
    protected function dispatchMacros(IComponent $component, $macrosURI, array $params, SplStack $callStack, $matchedMacrosURI = '')
    {
        $routeResult = $component->getRouter()->match($macrosURI);
        $routeMatches = $routeResult->getMatches();

        $context = $this->createDispatchContext($component);
        $callStack->push($context);

        $context
            ->setRouteParams($routeMatches)
            ->setBaseUrl($matchedMacrosURI)
            ->setCallStack(clone $callStack);

        if (isset($routeMatches[IComponent::MATCH_COMPONENT]) && $component->hasChildComponent($routeMatches[IComponent::MATCH_COMPONENT])) {

            $childComponent = $component->getChildComponent($routeMatches[IComponent::MATCH_COMPONENT]);
            $matchedMacrosURI .= $routeResult->getMatchedUrl();

            return $this->dispatchMacros($childComponent, $routeResult->getUnmatchedUrl(), $params, $callStack, $matchedMacrosURI);

        } else {
            return $component->getMacros(ltrim($macrosURI, self::MACROS_URI_SEPARATOR), $params)
                ->setContext($context);
        }
    }

    /**
     * Вызывает макрос.
     * @param IMacros $macros
     * @throws UnexpectedValueException если макрос вернул неверный результат
     * @return IView|string
     */
    protected function invokeMacros(IMacros $macros)
    {
        $macrosResult = $macros();

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
     * @throws HttpForbidden если доступ к ресурсу не разрешен.
     * @return IHTTPComponentResponse
     */
    protected function processRequest(IComponent $component, $routePath, SplStack $callStack, $matchedRoutePath = '')
    {
        $routeResult = $component->getRouter()->match($routePath);
        $routeMatches = $routeResult->getMatches();

        $context = $this->createDispatchContext($component);
        $callStack->push($context);

        $context
            ->setRouteParams($routeMatches)
            ->setBaseUrl($matchedRoutePath)
            ->setCallStack(clone $callStack);

        if (isset($routeMatches[IComponent::MATCH_COMPONENT])) {

            return $this->processChildComponentRequest($component, $routeResult, $callStack, $matchedRoutePath);

        } elseif (isset($routeMatches[IComponent::MATCH_CONTROLLER]) && !$routeResult->getUnmatchedUrl()) {

            return $this->processControllerRequest($component, $context, $callStack, $routeMatches);

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
         * @var IDispatchContext $context
         */
        foreach ($callStack as $context) {

            $component = $context->getComponent();
            if (!$component->hasController(IComponent::ERROR_CONTROLLER)) {
                continue;
            }

            $errorController = $component->getController(IComponent::ERROR_CONTROLLER, [$e])
                ->setContext($context)
                ->setRequest($this->currentRequest);


            try {
                $errorResponse = $this->invokeController($errorController);
                $layoutResponse = $this->processResponse($errorResponse, $callStack);
            } catch (Exception $e) {
                continue;
            }
            $content = (string) $layoutResponse->getContent();

            if ($this->controllerViewRenderErrorInfo) {
                list ($renderException) = $this->controllerViewRenderErrorInfo;
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
     * Проверяет наличие разрешений на ресурс
     * @param IComponent $component компонент, которому принадлежит ресурс.
     * @param IACLResource $resource ресурс
     * @return bool
     */
    protected function checkPermissions(IComponent $component, IACLResource $resource)
    {
        $authManager = $this->getDefaultAuthManager();
        if (!$authManager->isAuthenticated()) {
            return false;
        }

        $identity = $authManager->getStorage()->getIdentity();

        if (!$identity instanceof IACLRoleProvider) {
            return false;
        }

        $aclManager = $component->getACLManager();

        foreach ($identity->getRoles($component) as $roleName) {
            if ($aclManager->isAllowed($roleName, $resource->getACLResourceName(), 'execute')) {
                return true;
            }
        }

        return false;

    }

    /**
     * Вызывает контроллер компонента.
     * @param IController $controller контроллер
     * @throws UnexpectedValueException если контроллер вернул неожиданный результат
     * @return IHTTPComponentResponse
     */
    protected function invokeController(IController $controller)
    {
        $componentResponse = $controller();

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
     * @param IHTTPComponentResponse $response
     * @param SplStack $callStack
     * @return IHTTPComponentResponse
     */
    protected function processResponse(IHTTPComponentResponse $response, SplStack $callStack)
    {
        /**
         * @var IDispatchContext $context
         */
        foreach ($callStack as $context) {
            if ($response->isProcessable()) {

                $component = $context->getComponent();

                if (!$component->hasController(IComponent::LAYOUT_CONTROLLER)) {
                    continue;
                }

                $layoutController = $component->getController(IComponent::LAYOUT_CONTROLLER, [$response])
                    ->setContext($context)
                    ->setRequest($this->currentRequest);
                $response = $this->invokeController($layoutController);
            }
        }

        return $response;
    }

    /**
     * Создает контекст вызова компонента.
     * @param IComponent $component
     * @return IDispatchContext
     */
    protected function createDispatchContext(IComponent $component)
    {
        return new DispatchContext($component, $this);
    }

    /**
     * Возвращает результат работы дочернего компонента.
     * @param IComponent $component
     * @param IRouteResult $routeResult
     * @param SplStack $callStack
     * @param string $matchedRoutePath
     * @throws HttpForbidden если дочерний компонент не существует
     * @throws HttpNotFound если доступ к дочернему компоненту не разрешен
     * @return IHTTPComponentResponse
     */
    private function processChildComponentRequest(IComponent $component, IRouteResult $routeResult, SplStack $callStack, $matchedRoutePath)
    {
        $routeMatches = $routeResult->getMatches();
        if (!$component->hasChildComponent($routeMatches[IComponent::MATCH_COMPONENT])) {

            throw new HttpNotFound(
                $this->translate(
                    'Child component "{name}" not found.',
                    ['name' => $routeMatches[IComponent::MATCH_COMPONENT]]
                )
            );
        }

        /**
         * @var IComponent|IACLResource $childComponent
         */
        $childComponent = $component->getChildComponent($routeMatches[IComponent::MATCH_COMPONENT]);

        if ($childComponent instanceof IACLResource && !$this->checkPermissions($component, $childComponent)) {

            throw new HttpForbidden(
                $this->translate(
                    'Cannot execute component "{path}". Access denied.',
                    ['path' => $childComponent->getPath()]
                )
            );
        }

        $matchedRoutePath .= $routeResult->getMatchedUrl();

        return $this->processRequest($childComponent, $routeResult->getUnmatchedUrl(), $callStack, $matchedRoutePath);
    }

    /**
     * Возвращает результат работы контроллера компонента.
     * @param IComponent $component
     * @param IDispatchContext $context
     * @param SplStack $callStack
     * @param array $routeMatches
     * @throws HttpForbidden
     * @throws HttpNotFound
     * @return IHTTPComponentResponse
     */
    private function processControllerRequest(IComponent $component, IDispatchContext $context, SplStack $callStack, array $routeMatches)
    {
        if (!$component->hasController($routeMatches[IComponent::MATCH_CONTROLLER])) {
            throw new HttpNotFound(
                $this->translate(
                    'Controller "{name}" not found.',
                    ['name' => $routeMatches[IComponent::MATCH_CONTROLLER]]
                )
            );
        }

        /**
         * @var IController|IACLResource $controller
         */
        $controller = $component->getController($routeMatches[IComponent::MATCH_CONTROLLER])
            ->setContext($context)
            ->setRequest($this->currentRequest);

        if ($controller instanceof IACLResource && !$this->checkPermissions($component, $controller)) {
            throw new HttpForbidden(
                $this->translate(
                    'Cannot execute controller "{name}" for component "{path}". Access denied.',
                    [
                        'name' => $controller->getName(),
                        'path' => $component->getPath()
                    ]
                )
            );
        }

        $componentResponse = $this->invokeController($controller);

        return $this->processResponse($componentResponse, $callStack);
    }

    /**
     * Возвращает информацию о контексте вызова макроса.
     * @param string $macrosURI путь макроса
     * @throws RuntimeException если контекст не существует
     * @return array
     */
    private function resolveMacrosContext(&$macrosURI)
    {
        if (strpos($macrosURI, self::MACROS_URI_SEPARATOR) !== 0) {
            if (!$this->currentContext) {
                throw new RuntimeException(
                    $this->translate(
                        'Context for executing macros "{macros}" is unknown.',
                        ['macros' => $macrosURI]
                    )
                );
            }

            $macrosURI = self::MACROS_URI_SEPARATOR . $macrosURI;

            return [
                $this->currentContext->getComponent(),
                clone $this->currentContext->getCallStack(),
                $this->currentContext->getBaseUrl()
            ];
        }

        return [
            $this->initialComponent,
            $this->createCallStack(),
            ''
        ];
    }

    /**
     * Создает пустой стек вызова.
     * @return SplStack
     */
    private function createCallStack()
    {
        $callStack = new SplStack();
        $callStack->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO | SplDoublyLinkedList::IT_MODE_DELETE);

        return $callStack;
    }


}
 