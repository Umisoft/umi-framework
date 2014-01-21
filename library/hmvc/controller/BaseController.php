<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller;

use umi\hmvc\component\IComponent;
use umi\hmvc\dispatcher\http\IHTTPComponentRequest;
use umi\hmvc\dispatcher\http\HTTPComponentResponse;
use umi\hmvc\dispatcher\http\IHTTPComponentResponse;
use umi\hmvc\exception\RequiredDependencyException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Базовый класс контроллера.
 */
abstract class BaseController implements IController, ILocalizable
{
    use TLocalizable;

    /**
     * @var IHTTPComponentRequest $request
     */
    private $request;

    public function setHTTPComponentRequest(IHTTPComponentRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Возвращает контекст вызова контроллера.
     * @throws RequiredDependencyException если запрос не был установлен
     * @return IHTTPComponentRequest
     */
    protected function getHTTPComponentRequest()
    {
        if (!$this->request) {
            throw new RequiredDependencyException(
                sprintf('HTTP component request is not injected in controller "%s".', get_class($this))
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
        return $this->getHTTPComponentRequest()->getComponent();
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
    protected function createDisplayResponse($templateName, array $variables)
    {
        return $this->createHTTPComponentResponse()
            ->setContent(
                new ControllerView($this->getHTTPComponentRequest(), $templateName, $variables)
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
}
