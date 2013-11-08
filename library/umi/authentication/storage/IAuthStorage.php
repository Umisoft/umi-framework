<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\storage;

/**
 * Класс storage для аутентификации.
 */
interface IAuthStorage
{
    /**
     * Сохраняет объект
     * @param mixed $object
     * @return self
     */
    public function setIdentity($object);

    /**
     * Возвращает объект
     * @return mixed
     */
    public function getIdentity();

    /**
     * Проверяет существует ли сохраненный объект
     * @return bool
     */
    public function hasIdentity();

    /**
     * Удаляет объект
     * @return self
     */
    public function clearIdentity();
}