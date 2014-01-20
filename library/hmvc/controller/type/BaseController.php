<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller\type;

use umi\hmvc\component\IComponent;
use umi\hmvc\component\response\HTTPComponentResponse;
use umi\hmvc\component\response\IHTTPComponentResponse;
use umi\hmvc\controller\IController;
use umi\hmvc\exception\RequiredDependencyException;
use umi\hmvc\view\content\Content;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Базовый класс контроллера.
 */
abstract class BaseController implements IController, ILocalizable
{
    use TLocalizable;

    /**
     * @var IComponent $component компонент, которому принадлежит контроллер
     */
    private $component;

    /**
     * {@inheritdoc}
     */
    public function setComponent(IComponent $component)
    {
        $this->component = $component;
        return $this;
    }

    /**
     * Возвращает компонент, которому принадлежит контроллер.
     * @throws RequiredDependencyException если контроллер не был установлен
     * @return IComponent
     */
    protected function getComponent()
    {
        if (!$this->component) {
            throw new RequiredDependencyException(
                sprintf('Component is not injected in controller "%s".', __CLASS__)
            );
        }
        return $this->component;
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

     * @param string $template имя шаблона
     * @param array $variables переменные
     * @return IHTTPComponentResponse
     */
    protected function createDisplayResponse($template, array $variables)
    {
        return $this->createHTTPComponentResponse()
                ->setContent(
                    new Content($this->getComponent()->getView(), $template, $variables)
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
