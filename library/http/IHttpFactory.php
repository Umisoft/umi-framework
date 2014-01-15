<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http;

use umi\http\request\IRequest;
use umi\http\response\IResponse;

/**
 * Фабрика HTTP сущностей.
 */
interface IHttpFactory
{
    /**
     * Возвращает HTTP запрос к серверу.
     * @return IRequest
     */
    public function getRequest();

    /**
     * Создает HTTP ответ к серверу.
     * @return IResponse
     */
    public function createResponse();
}