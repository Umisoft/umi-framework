<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\rbac\toolbox\factory;

use umi\rbac\exception\InvalidArgumentException;
use umi\rbac\IRbacRole;
use umi\rbac\IRoleFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика ролей Rbac.
 */
class RoleFactory implements IRoleFactory, IFactory
{

    use TFactory;

    /**
     * @var string $roleClass класс Rbac роли
     */
    public $roleClass = 'umi\rbac\RbacRole';

    /**
     * {@inheritdoc}
     */
    public function createRole(array $permissions, array $roles = [])
    {
        foreach ($roles as $role) {
            if (!($role instanceof IRbacRole)) {
                throw new InvalidArgumentException($this->translate(
                    'Role must implement IRbacRole.'
                ));
            }

            $permissions = array_merge($permissions, $role->getPermissions());
        }

        $permissions = array_unique($permissions);

        return $this->createInstance($this->roleClass, [$permissions], ['umi\rbac\IRbacRole']);
    }

}
