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
 * Трейт доступа к контейнеру как к массиву.
 * Реализует интерфейс \ArrayAccess для IContainer.
 */
trait TArrayAccess
{
    /**
     * Возвращает значение аттрибута из контейнера
     * @param string $attribute аттрибут
     * @return mixed значение
     */
    public abstract function get($attribute);

    /**
     * Устанавливает значение аттрибута из контейнера
     * @param string $attribute аттрибут
     * @param mixed $value значение
     * @return self
     */
    public abstract function set($attribute, $value);

    /**
     * Проверяет, существет ли аттрибут в контейнере
     * @param string $attribute аттрибут
     * @return bool true - если существует, иначе false
     */
    public abstract function has($attribute);

    /**
     * Удаляет аттрибут из контейнера
     * @param string $attribute
     * @return self
     */
    public abstract function del($attribute);

    /**
     * Синононим has()
     * @param string $offset аттрибут
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Синоним get()
     * @param string $offset аттрибут
     * @return mixed значение
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Синоним set()
     * @param string $offset аттрибут
     * @param mixed $value значение
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * Синоним del()
     * @param string $offset аттрибут
     * @return $this
     */
    public function offsetUnset($offset)
    {
        return $this->del($offset);
    }
}