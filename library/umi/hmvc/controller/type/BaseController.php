<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller\type;

use umi\hmvc\component\response\IComponentResponse;
use umi\hmvc\component\response\IComponentResponseAware;
use umi\hmvc\component\response\IComponentResponseFactory;
use umi\hmvc\component\response\model\DisplayModel;
use umi\hmvc\component\response\TComponentResponseAware;
use umi\hmvc\controller\IController;
use umi\hmvc\exception\RequiredDependencyException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Абстрактный базовый класс контроллера.
 * Реализует helper методы для контроллеров.
 */
abstract class BaseController implements IController, IComponentResponseAware, ILocalizable
{
    use TLocalizable;

    /**
     * @var IComponentResponseFactory $responseFactory
     */
    private $responseFactory;

    /**
     * {@inheritdoc}
     */
    public function setComponentResponseFactory(IComponentResponseFactory $factory)
    {
        $this->responseFactory = $factory;
    }

    /**
     * Создает HTTP ответ компонента.
     * @param string $content содержимое ответа
     * @param int $code код ответа
     * @return IComponentResponse
     */
    protected function createPlainResponse($content, $code = 200)
    {
        return $this->getComponentResponseFactory()
            ->createComponentResponse()
            ->setCode($code)
            ->setContent($content);
    }

    /**
     * Создает HTTP ответ компонента с содержимым, требующим отображения.
     *
     * Этот ответ пройдет через View слой компонента.
     *
     * @param string $template имя шаблона
     * @param array $variables переменные
     * @return IComponentResponse
     */
    protected function createDisplayResponse($template, array $variables)
    {
        return $this->getComponentResponseFactory()
            ->createComponentResponse()
                ->setContent(
                    new DisplayModel($template, $variables)
                );
    }

    /**
     * Устанавливает в ответ заголовок переадресации.
     * @param string $url URL для переадресации
     * @param int $code HTTP статус переадресации
     * @return IComponentResponse HTTP ответ
     */
    protected function createRedirectResponse($url, $code = 301)
    {
        $response = $this->getComponentResponseFactory()
            ->createComponentResponse()
                ->setCode($code)
                ->getHeaders()
                    ->setHeader('Location', $url);

        return $response;
    }

    /**
     * Возвращает фабрику для HTTP ответов компонента.
     * @return IComponentResponseFactory
     * @throws RequiredDependencyException если фабрика не была внедрена
     */
    private function getComponentResponseFactory()
    {
        if (!$this->responseFactory) {
            throw new RequiredDependencyException(
                sprintf('Authentication factory is not injected in class "%s".', __CLASS__)
            );
        }

        return $this->responseFactory;
    }
}
