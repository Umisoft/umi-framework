<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\entity\ns;

use umi\spl\container\IContainer;

/**
 * Интерфейс пространства имен сессии.
 */
interface ISessionNamespace extends IContainer, \Iterator, \Countable, \ArrayAccess
{
    /**
     * Тип для глобального пространства имен.
     */
    const TYPE_GLOBAL = '__GLOBAL__';

    /**
     * Возвращает имя пространства имен.
     * @return string
     */
    public function getName();

    /**
     * Возвращает значения сессии в виде массива.
     * @return array
     */
    public function toArray();

    /**
     * Возвращает значение заданного ключа метаданных.
     * @param string $key ключ
     * @return mixed
     */
    public function getMetadata($key);

    /**
     * Устанавливает значение заданного ключа метаданных.
     * @param string $key ключ
     * @param mixed $value значение
     * @return self
     */
    public function setMetadata($key, $value);

    /**
     * Очищает пространство имен сессии.
     * После вызова данного метода контейнер хранения будет переинициализирован.
     * @return self
     */
    public function clear();
}