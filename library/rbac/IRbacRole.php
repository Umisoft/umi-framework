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
 * Интерфейс роли Rbac политики доступа.
 */
interface IRbacRole
{

    /**
     * Проверяет, есть ли заданное разрешение у роли.
     * @param string $permission разрешение
     * @param null|IRbacAssert|callable $assertion динамическое правило проверки.
     * @return bool
     */
    public function hasPermission($permission, $assertion = null);

    /**
     * Возвращает массив всех разрешений для роли.
     * @return array
     */
    public function getPermissions();
}