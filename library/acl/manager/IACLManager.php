<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\acl\manager;

use umi\acl\exception\NonexistentEntityException;
use umi\acl\exception\RuntimeException;

/**
 * ACL-менеджер.
 */
interface IACLManager
{

    /**
     * Добавляет роль.
     * @param $roleName имя роли
     * @param array $parentRoleNames список имен родительских ролей
     * @throws RuntimeException если невозможно добавить роль
     * @return self
     */
    public function addRole($roleName, array $parentRoleNames = []);

    /**
     * Добавляет ресурс.
     * @param $resourceName имя ресурса
     * @param array $operations список доступных операций над ресурсом
     * @return self
     */
    public function addResource($resourceName, array $operations = []);

    /**
     * Проверяет, существует ли роль.
     * @param $roleName имя роли
     * @return bool
     */
    public function hasRole($roleName);

    /**
     * Проверяет, существует ли ресурс.
     * @param $resourceName имя ресурса
     * @return bool
     */
    public function hasResource($resourceName);

    /**
     * Проверяет существует ли операция над ресурсом.
     * @param $resourceName имя ресурса
     * @param $operationName имя операции
     * @return bool
     */
    public function hasResourceOperation($resourceName, $operationName);

    /**
     * Выставляет для роли разрешенные для ресурса операции.
     * Если операции не указаны, разрешения выставляются на все.
     * @param string $roleName имя роли
     * @param string $resourceName имя ресурса
     * @param array $operations список разрешенных операций
     * @throws NonexistentEntityException если роль, ресурс или операция не существуют
     * @return $this
     */
    public function allow($roleName, $resourceName, array $operations = []);

    /**
     * Проверяет разрешение на операцию над ресурсом для роли.
     * @param string $roleName имя роли
     * @param string $resourceName имя ресурса
     * @param string $operationName имя операции
     * @throws NonexistentEntityException если роль, ресурс или операция не существуют
     * @return bool
     */
    public function isAllowed($roleName, $resourceName, $operationName);

}
 