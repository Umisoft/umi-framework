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
 * Фабрика ролей для компонента Rbac.
 */
interface IRoleFactory
{
    /**
     * Создает новую Rbac роль на основе разрешений и родительских ролей.
     * @param array $permissions разрешения
     * @param IRbacRole[] $roles родительские роли
     * @return IRbacRole созданная роль
     */
    public function createRole(array $permissions, array $roles = []);
}