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
     * Алиас, задающий все ресурсы
     */
    const RESOURCE_ALL = '*';
    /**
     * Алиас, задающий все операции
     */
    const OPERATION_ALL = '*';

    /**
     * Добавляет роль.
     * @param string $roleName имя роли
     * @param array $parentRoleNames список имен родительских ролей
     * @throws RuntimeException если невозможно добавить роль
     * @return self
     */
    public function addRole($roleName, array $parentRoleNames = []);

    /**
     * Добавляет ресурс.
     * @param string $resourceName имя ресурса
     * @return self
     */
    public function addResource($resourceName);

    /**
     * Проверяет, существует ли роль.
     * @param string $roleName имя роли
     * @return bool
     */
    public function hasRole($roleName);

    /**
     * Проверяет, существует ли ресурс.
     * @param string $resourceName имя ресурса
     * @return bool
     */
    public function hasResource($resourceName);

    /**
     * Устанавливает разрешения для операции над ресурсом.
     * @param string $roleName имя роли
     * @param string $resourceName имя ресурса
     * @param string $operationName имя операции
     * @param callable $assertion дополнительная динамическая проверка разрешения
     * @return $this
     */
    public function allow($roleName, $resourceName = self::RESOURCE_ALL, $operationName = self::OPERATION_ALL, callable $assertion = null);

    /**
     * Проверяет разрешение на операцию над ресурсом для роли.
     * @param string $roleName имя роли
     * @param string $resourceName имя ресурса
     * @param string $operationName имя операции
     * @throws NonexistentEntityException если роль, ресурс или операция не существуют
     * @return bool
     */
    public function isAllowed($roleName, $resourceName = self::RESOURCE_ALL, $operationName = self::OPERATION_ALL);

}
 