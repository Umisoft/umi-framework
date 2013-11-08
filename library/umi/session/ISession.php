<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session;

use umi\session\entity\ns\ISessionNamespace;

/**
 * Сервис сессии.
 */
interface ISession
{
    /**
     * Регистрирует пространство имен сессии.
     * @param string $name имя
     * @param array $validators валидаторы
     * @return self
     */
    public function registerNamespace($name, array $validators = []);

    /**
     * Проверяет существование пространства имен.
     * @param string $name имя
     * @return bool
     */
    public function hasNamespace($name);

    /**
     * Возвращает экземпляр ранее зарегистрированного пространства имен.
     * @param string $name имя
     * @return ISessionNamespace
     */
    public function getNamespace($name);

    /**
     * Удаляет пространство имен сесии.
     * @param string $name
     * @return ISessionNamespace
     */
    public function deleteNamespace($name);

    /**
     * Чистит сессию.
     * @return self
     */
    public function clearSession();

    /**
     * Устанавливает хранилище для сессии.
     * @param string $type тип хранилища
     * @param array $options опции
     * @return bool
     */
    public function setStorage($type, array $options = []);
}