<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\spl\container;

/**
 * Интерфейс SPL контейнера.
 */
interface IContainer
{
    /**
     * Возвращает значение аттрибута из контейнера
     * @param string $attribute аттрибут
     * @return mixed значение
     */
    public function get($attribute);

    /**
     * Устанавливает значение аттрибута из контейнера
     * @param string $attribute аттрибут
     * @param mixed $value значение
     * @return self
     */
    public function set($attribute, $value);

    /**
     * Проверяет, существет ли аттрибут в контейнере
     * @param string $attribute аттрибут
     * @return bool true - если существует, иначе false
     */
    public function has($attribute);

    /**
     * Удаляет аттрибут из контейнера
     * @param string $attribute
     * @return self
     */
    public function del($attribute);
}