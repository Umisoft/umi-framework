<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\rbac\toolbox;

use umi\rbac\IRoleFactory;
use umi\toolkit\toolbox\IToolbox;

/**
 * Интерфейс инструментов для Rbac политики доступа.
 */
interface IRbacTools extends IToolbox
{
    /**
     * Возвращает фабрику ролей.
     * @return IRoleFactory
     */
    public function getRoleFactory();
}