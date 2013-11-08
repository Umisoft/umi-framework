<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller\type;

use umi\hmvc\component\response\IComponentResponse;
use umi\hmvc\component\response\IComponentResponseAware;
use umi\hmvc\component\response\TComponentResponseAware;
use umi\hmvc\controller\IController;
use umi\hmvc\controller\result\IControllerResultAware;
use umi\hmvc\controller\result\TControllerResultAware;

/**
 * Абстрактный базовый класс контроллера.
 * Реализует helper методы для контроллеров.
 */
abstract class BaseController implements IController, IComponentResponseAware, IControllerResultAware
{
    use TComponentResponseAware;
    use TControllerResultAware;

    /**
     * Устанавливает в ответ заголовок переадресации.
     * @param string $url URL для переадресации
     * @param int $status HTTP статус переадресации
     * @return IComponentResponse HTTP ответ
     */
    protected function createRedirectResponse($url, $status = 301)
    {
        $response = $this->createComponentResponse();

        $response
            ->setCode($status)
            ->getHeaders()
            ->setHeader('Location', $url);

        return $response;
    }
}