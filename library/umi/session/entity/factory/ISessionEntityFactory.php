<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\entity\factory;

use SessionHandlerInterface;
use umi\session\entity\ns\ISessionNamespace;
use umi\session\entity\validator\ISessionValidator;

/**
 * Фабрика для создания пространств имен в сессии.
 */
interface ISessionEntityFactory
{
    /**
     * Создает пространство имен сессии.
     * @param string $name имя
     * @return ISessionNamespace
     */
    public function createSessionNamespace($name);

    /**
     * Создает валидатор сесии заданного типа.
     * @param string $type тип валидатора
     * @param array|mixed $options опции валидатора
     * @return ISessionValidator
     */
    public function createSessionValidator($type, $options);

    /**
     * Создает объект хранилища сессии.
     * @param string $type тип хранилища
     * @param array $options опции хранилища сесии
     * @return SessionHandlerInterface
     */
    public function createSessionStorage($type, array $options = []);
}