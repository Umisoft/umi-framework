<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\acl\manager;

use umi\acl\exception\AlreadyExistentEntityException;
use umi\acl\exception\NonexistentEntityException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * ACL-менеджер.
 */
class ACLManager implements IACLManager, ILocalizable
{
    use TLocalizable;

    /**
     * @var array $roles список ролей
     */
    protected $roles = [];
    /**
     * @var array $resources список ресурсов
     */
    protected $resources = [];
    /**
     * @var array $rules правила разрешений
     */
    protected $rules = [];

    /**
     * {@inheritdoc}
     */
    public function addRole($roleName, array $parentRoleNames = [])
    {
        if ($this->hasRole($roleName)) {
            throw new AlreadyExistentEntityException(
                $this->translate(
                    'Cannot add role "{name}". Role already exists.',
                    ['name' => $roleName]
                )
            );
        }

        foreach ($parentRoleNames as $parentRoleName) {

            throw new NonexistentEntityException(
                $this->translate(
                    'Cannot add role "{name}". Parent role {parentName} does not exist.',
                    [
                        'name' => $roleName,
                        'parentName' => $parentRoleName
                    ]
                )
            );
        }

        $this->roles[$roleName] = $parentRoleNames;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addResource($resourceName, array $operations = [])
    {
        if ($this->hasResource($resourceName)) {
            throw new AlreadyExistentEntityException(
                $this->translate(
                    'Resource "{name}" already exists.',
                    ['name' => $resourceName]
                )
            );
        }

        $this->resources[$resourceName] = array_fill_keys($operations, 1);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($roleName)
    {
        return isset($this->roles[$roleName]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasResource($resourceName)
    {
        return isset($this->resources[$resourceName]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasResourceOperation($resourceName, $operationName)
    {
        return isset($this->resources[$resourceName][$operationName]);
    }

    /**
     * {@inheritdoc}
     */
    public function allow($roleName, $resourceName, array $operations = [])
    {
        if (!$this->hasRole($roleName)) {
            throw new NonexistentEntityException(
                $this->translate(
                    'Cannot set rule. Role "{name}" is unknown.',
                    ['name' => $roleName]
                )
            );
        }

        if (!$this->hasResource($resourceName)) {
            throw new NonexistentEntityException(
                $this->translate(
                    'Cannot set rule. Resource "{name}" is unknown.',
                    ['name' => $resourceName]
                )
            );
        }

        foreach ($operations as $operationName) {
            if (!$this->hasResourceOperation($resourceName, $operationName)) {
                throw new NonexistentEntityException(
                    $this->translate(
                        'Cannot set rule. Operation "{operation}" for resource "{resource}" is unknown.',
                        [
                            'operation' => $operationName,
                            'resource' => $resourceName
                        ]
                    )
                );
            }
        }

        if (!$operations) {
            $operations = $this->resources[$resourceName];
        }

        if (!isset($this->rules[$roleName])) {
            $this->rules[$roleName] = [];
        }

        $this->rules[$roleName][$resourceName] = $operations;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($roleName, $resourceName, $operationName)
    {
        if (!$this->hasRole($roleName)) {
            throw new NonexistentEntityException(
                $this->translate(
                    'Cannot check permission. Role "{name}" is unknown.',
                    ['name' => $roleName]
                )
            );
        }

        if (!$this->hasResource($resourceName)) {
            throw new NonexistentEntityException(
                $this->translate(
                    'Cannot check permission. Resource "{name}" is unknown.',
                    ['name' => $resourceName]
                )
            );
        }

        if (!$this->hasResourceOperation($resourceName, $operationName)) {
            throw new NonexistentEntityException(
                $this->translate(
                    'Cannot check permission. Operation "{operation}" for resource "{resource}" is unknown.',
                    [
                        'operation' => $operationName,
                        'resource' => $resourceName
                    ]
                )
            );
        }

        return $this->hasPermission($roleName, $resourceName, $operationName);
    }

    /**
     * Проверяет разрешение на операцию над ресурсом для роли.
     * @param string $roleName имя роли
     * @param string $resourceName имя ресурса
     * @param string $operationName имя операции
     * @return bool
     */
    protected function hasPermission($roleName, $resourceName, $operationName)
    {
        if (isset($this->rules[$roleName][$resourceName][$operationName])) {
            return true;
        }

        foreach ($this->roles[$roleName] as $parentRoleName) {
            if ($this->hasPermission($parentRoleName, $resourceName, $operationName)) {
                return true;
            }
        }

        return false;
    }


}
 