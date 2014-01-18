<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component\response;

use umi\hmvc\component\IComponent;
use umi\http\response\IResponse;

/**
 * Результат работы компонента.
 */
interface IHTTPComponentResponse extends IResponse
{
    /**
     * Останавливает обработку результата родительскими компонентами.
     * @return self
     */
    public function stopProcessing();

    /**
     * Возвращает статус может ли результат быть обработан родительскими компонентами.
     * @return bool
     */
    public function isProcessable();

    /**
     * Возвращает компонент.
     * @return IComponent
     */
    public function getComponent();
}