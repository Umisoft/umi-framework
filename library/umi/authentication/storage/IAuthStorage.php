<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\authentication\storage;

use umi\authentication\exception\RuntimeException;

/**
 * Интерфейс хранилища характеристик субъекта аутентификации.
 */
interface IAuthStorage
{
    /**
     * Устанавливает опции хранилища.
     * @param array $options
     * @return self
     */
    public function setOptions(array $options);

    /**
     * Сохраняет характеристики субъекта аутентификации.
     * @param mixed $identity
     * @return self
     */
    public function setIdentity($identity);

    /**
     * Возвращает характеристики субъекта.
     * @throws RuntimeException если не удалось загрузить характеристики
     * @return mixed
     */
    public function getIdentity();

    /**
     * Проверяет, были ли сохранены характеристики субъекта аутентификации в хранилище.
     * @return bool
     */
    public function hasIdentity();

    /**
     * Удаляет характеристики субъекта аутентификации.
     * @return self
     */
    public function clearIdentity();
}