<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\context;

use umi\hmvc\component\request\IComponentRequest;

/**
 * Интерфейс для поддержки внедрения запроса из контекста.
 */
interface IRequestContext
{
    /**
     * Устанавливает контекстно-зависимый запрос.
     * @param IComponentRequest $request
     */
    public function setContextRequest(IComponentRequest $request = null);
}
 