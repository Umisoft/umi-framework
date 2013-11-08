<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\toolbox;

use umi\authentication\IAuthenticationFactory;
use umi\toolkit\toolbox\IToolbox;

/**
 * Интерфейс инструментов аутентификации.
 */
interface IAuthenticationTools extends IToolbox
{
    /**
     * Короткий alias
     */
    const ALIAS = 'authentication';

    /**
     * Возвращает фабрику объектов аутентификациию
     * @return IAuthenticationFactory
     */
    public function getAuthenticationFactory();
}