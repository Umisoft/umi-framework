<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\acl;

use umi\acl\exception\RuntimeException;
use umi\acl\exception\UnexpectedValueException;
use umi\acl\manager\IACLManager;

/**
 * Фабрика для сущностей ACL.
 */
interface IACLFactory
{
    /**
     * Опция для конфигурации ролей
     */
    const OPTION_ROLES = 'roles';
    /**
     * Опция для конфигурации ресурсов
     */
    const OPTION_RESOURCES = 'resources';
    /**
     * Опция для конфигурации правил назначения прав
     */
    const OPTION_RULES = 'rules';

    /**
     * Создает и конфигурирует ACL-менеджер.
     * @param array $config конфигурация менеджера
     * @throws UnexpectedValueException при неверном формате конфигурации
     * @throws RuntimeException при ошибках конфигурирования
     * @return IACLManager
     */
    public function createACLManager(array $config = []);
}
 