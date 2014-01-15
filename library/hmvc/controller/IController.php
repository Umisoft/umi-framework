<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller;

use umi\hmvc\component\request\IComponentRequest;
use umi\hmvc\component\response\IComponentResponse;

/**
 * Интерфейс контроллера.
 */
interface IController
{
    /**
     * Вызывает контроллер. Передает в контроллер HTTP запрос.
     * @param IComponentRequest $request HTTP запрос
     * @return IComponentResponse результат работы контроллера
     */
    public function __invoke(IComponentRequest $request);
}
