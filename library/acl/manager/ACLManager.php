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
    protected $resources = [
        self::RESOURCE_ALL => null
    ];
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

            if (!$this->hasRole($parentRoleName)) {
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
        }

        $this->roles[$roleName] = $parentRoleNames;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addResource($resourceName)
    {
        if ($this->hasResource($resourceName)) {
            throw new AlreadyExistentEntityException(
                $this->translate(
                    'Resource "{name}" already exists.',
                    ['name' => $resourceName]
                )
            );
        }
        $this->resources[$resourceName] = null;

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
        return array_key_exists($resourceName, $this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function allow($roleName, $resourceName = self::RESOURCE_ALL, $operationName = self::OPERATION_ALL, callable $assertion = null)
    {
        if (!$this->hasRole($roleName)) {
            throw new NonexistentEntityException(
                $this->translate(
                    'Cannot set rule. Role "{name}" is unknown.',
                    ['name' => $roleName]
                )
            );
        }

        if ($this->hasResource($resourceName)) {
            throw new NonexistentEntityException(
                $this->translate(
                    'Cannot set rule. Resource "{name}" is unknown.',
                    ['name' => $resourceName]
                )
            );
        }

        if (!isset($this->rules[$roleName])) {
            $this->rules[$roleName] = [];
        }
        if (!isset($this->rules[$roleName][$resourceName])) {
            $this->rules[$roleName][$resourceName] = [];
        }

        $this->rules[$roleName][$resourceName][$operationName] = $assertion ?: true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($roleName, $resourceName = self::RESOURCE_ALL, $operationName = self::OPERATION_ALL)
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

        if (isset($this->rules[$roleName][self::RESOURCE_ALL])) {
            if (isset($this->rules[$roleName][self::RESOURCE_ALL][self::OPERATION_ALL])) {
                $assertion = $this->rules[$roleName][self::RESOURCE_ALL][self::OPERATION_ALL];
            } elseif ($this->rules[$roleName][self::RESOURCE_ALL][$operationName]) {
                $assertion = $this->rules[$roleName][self::RESOURCE_ALL][$operationName];
            }

            if (isset($assertion) && $assertion !== true) {
                return $assertion($this, $roleName, $resourceName, $operationName);
            }
        }

        if (isset($this->rules[$roleName][$resourceName][$operationName])) {

            $assertion = $this->rules[$roleName][$resourceName][$operationName];
            if ($assertion !== true) {
                return $assertion($this, $roleName, $resourceName, $operationName);
            }

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
 