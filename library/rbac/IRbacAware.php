<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\rbac;

/**
 * Интерфейс компонентов, поддерживающих создание Rbac ролей.
 */
interface IRbacAware
{
    /**
     * Устанавливает фабрику для создания сущностей Rbac.
     * @param IRoleFactory $rbacFactory фабрика
     */
    public function setRoleFactory(IRoleFactory $rbacFactory);
}