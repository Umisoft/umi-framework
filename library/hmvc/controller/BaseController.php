<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller;

use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\http\HTTPComponentResponse;
use umi\hmvc\dispatcher\http\IHTTPComponentResponse;
use umi\hmvc\dispatcher\IDispatchContext;
use umi\hmvc\exception\RequiredDependencyException;
use umi\hmvc\view\IView;
use umi\hmvc\view\View;
use umi\http\request\IRequest;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Базовый класс контроллера.
 */
abstract class BaseController implements IController, ILocalizable
{
    use TLocalizable;

    /**
     * @var string $name имя контроллера
     */
    protected $name;
    /**
     * @var IDispatchContext $context
     */
    private $context;
    /**
     * @var IRequest $request
     */
    private $request;

    /**
     * {@inheritdoc}
     */
    public function setContext(IDispatchContext $context)
    {
        $this->context = $context;

        return $this;
    }

    public function setRequest(IRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Возвращает контекст вызова контроллера.
     * @throws RequiredDependencyException если контекст не был установлен
     * @return IDispatchContext
     */
    protected function getContext()
    {
        if (!$this->context) {
            throw new RequiredDependencyException(
                sprintf('Dispatch context is not injected in controller "%s".', get_class($this))
            );
        }
        return $this->context;
    }

    /**
     * Возвращает HTTP-запрос.
     * @throws RequiredDependencyException если запрос не был установлен
     * @return IRequest
     */
    protected function getRequest()
    {
        if (!$this->request) {
            throw new RequiredDependencyException(
                sprintf('HTTP request is not injected in controller "%s".', get_class($this))
            );
        }
        return $this->request;
    }

    /**
     * Возвращает компонент, которому принадлежит контроллер.
     * @return IComponent
     */
    protected function getComponent()
    {
        return $this->getContext()->getComponent();
    }

    /**
     * Возвращает переменную из параметров маршрутизации.
     * @param string $name имя параметра
     * @param mixed $default значение по умолчанию
     * @return mixed значение из GET
     */
    protected function getRouteVar($name, $default = null)
    {
        $routeParams = $this->getContext()->getRouteParams();

        return isset($routeParams[$name]) ? $routeParams[$name] : $default;
    }

    /**
     * Возвращает переменную из параметров запроса.
     * @param string $name имя параметра
     * @param mixed $default значение по умолчанию
     * @param string $containerType тип контейнера параметров
     * @return mixed
     */
    public function getRequestVar($name, $default = null, $containerType = IRequest::GET)
    {
        return $this->getRequest()->getVar($containerType, $name, $default);
    }

    /**
     * Выполняет редирект на указанный маршрут текущего компонента.
     * @param string $routeName имя маршрута
     * @param array $params параметры маршрута
     * @param bool $useQuery использовать ли GET-параметры HTTP-запроса при построении URL
     * @param int $code код ответа
     * @return IHTTPComponentResponse
     */
    protected function redirectToRoute($routeName, array $params = [], $useQuery = false, $code = 301)
    {

        $baseUrl = $this->getContext()->getBaseUrl();

        $url = $baseUrl . $this->getComponent()->getRouter()->assemble($routeName, $params) ? : '/';

        if ($useQuery) {
            $getParams = $this->getRequest()->getParams(IRequest::GET)->toArray();
            if($getParams) {
                $url .= '?' . http_build_query($getParams);
            }
        }

        return $this->createRedirectResponse($url, $code);
    }

    /**
     * Создает HTTP ответ компонента.
     * @param string $content содержимое ответа
     * @param int $code код ответа
     * @return IHTTPComponentResponse
     */
    protected function createPlainResponse($content, $code = 200)
    {
        return $this->createHTTPComponentResponse()
            ->setCode($code)
            ->setContent($content);
    }

    /**
     * Создает HTTP ответ компонента с содержимым, требующим отображения.

     * Этот ответ пройдет через View слой компонента.

     * @param string $templateName имя шаблона
     * @param array $variables переменные
     * @return IHTTPComponentResponse
     */
    protected function createDisplayResponse($templateName, array $variables = [])
    {
        return $this->createHTTPComponentResponse()
            ->setContent(
                new View($this, $this->getContext(), $templateName, $variables)
            );
    }

    /**
     * Устанавливает в ответ заголовок переадресации.
     * @param string $url URL для переадресации
     * @param int $code HTTP статус переадресации
     * @return IHTTPComponentResponse HTTP ответ
     */
    protected function createRedirectResponse($url, $code = 301)
    {
        $response = $this->createHTTPComponentResponse();
        $response->setCode($code)
             ->getHeaders()
                ->setHeader('Location', $url);

        return $response;
    }

    /**
     * Возвращает HTTP ответ компонента.
     * @throws RequiredDependencyException если фабрика не была внедрена
     * @return IHTTPComponentResponse
     */
    protected function createHTTPComponentResponse()
    {
        return new HTTPComponentResponse($this->getComponent());
    }

    /**
     * Вызывает макрос своего компонента.
     * @param string $macrosURI имя макроса
     * @param array $params параметры вызова макроса
     * @return string|IView
     */
    protected function callMacros($macrosURI, array $params = [])
    {
        return $this->getContext()->getDispatcher()->executeMacros($macrosURI, $params);
    }
}
