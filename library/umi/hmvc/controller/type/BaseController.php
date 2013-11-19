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
use umi\hmvc\component\response\TComponentResponseAware;
use umi\hmvc\controller\IController;
use umi\hmvc\component\response\model\DisplayModel;

/**
 * Абстрактный базовый класс контроллера.
 * Реализует helper методы для контроллеров.
 */
abstract class BaseController implements IController, IComponentResponseAware
{
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

    protected function createResponse($content, $code = 200)
    {
        return $this->responseFactory
            ->createComponentResponse()
            ->setCode($code)
            ->setContent($content);
    }

    protected function createResultResponse($template, array $variables)
    {
        return $this->createResponse(
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
        $response = $this->createResponse(null);

        $response
            ->setCode($code)
            ->getHeaders()
            ->setHeader('Location', $url);

        return $response;
    }
}