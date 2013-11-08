<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\rbac;

use umi\rbac\exception\RequiredDependencyException;

/**
 * Трейт компонентов, поддерживающих создание Rbac ролей.
 */
trait TRbacAware
{
    /**
     * @var IRoleFactory $_rbacRoleFactory фабрика
     */
    private $_rbacRoleFactory;

    /**
     * Устанавливает фабрику для создания сущностей Rbac.
     * @param IRoleFactory $rbacFactory фабрика
     */
    public final function setRoleFactory(IRoleFactory $rbacFactory)
    {
        $this->_rbacRoleFactory = $rbacFactory;
    }

    /**
     * Создает новую Rbac роль на основе разрешений и родительских ролей.
     * @param array $permissions разрешения
     * @param IRbacRole[] $roles родительские роли
     * @throws RequiredDependencyException если фабрика не была внедрена
     * @return IRbacRole созданная роль
     */
    protected final function createRbacRole(array $permissions, array $roles = [])
    {
        if (!$this->_rbacRoleFactory) {
            throw new RequiredDependencyException(sprintf(
                'Rbac role factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_rbacRoleFactory->createRole($permissions, $roles);
    }
}