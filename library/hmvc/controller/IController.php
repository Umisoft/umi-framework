<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller;

use umi\hmvc\component\IComponent;
use umi\hmvc\component\request\IHTTPComponentRequest;
use umi\hmvc\component\response\IHTTPComponentResponse;

/**
 * Интерфейс контроллера.
 */
interface IController
{
    /**
     * Вызывает контроллер. Передает в контроллер HTTP запрос.
     * @param IHTTPComponentRequest $request HTTP запрос
     * @return IHTTPComponentResponse результат работы контроллера
     */
    public function __invoke(IHTTPComponentRequest $request);

    /**
     * Внедряет компонент, к которому принадлежит контроллер.
     * @param IComponent $component
     * @return self
     */
    public function setComponent(IComponent $component);
}
