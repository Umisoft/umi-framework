<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller;

use umi\hmvc\dispatcher\http\IHTTPComponentRequest;

/**
 * Интерфейс контроллера.
 */
interface IController
{

    /**
     * Устанавливает контекст вызова контроллера.
     * @param IHTTPComponentRequest $request
     * @return self
     */
    public function setHTTPComponentRequest(IHTTPComponentRequest $request);

}
