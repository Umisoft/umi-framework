<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\cache\engine;

/**
 * Интерфейс для реализации хранилища кэша.
 */
interface ICacheEngine
{

    /**
     * Добавляет значение нового ключа в кэш.<br />
     * Аналогичен методу set, но если ключ существует, операция не будет успешна.
     * @param string $key новый ключ
     * @param string $value значение
     * @param integer $ttl время жизни кэша в секундах. 0 - никогда не истекает.
     * @return bool успех операции
     */
    public function add($key, $value, $ttl = 0);

    /**
     * Устанавливает значение ключа в кэше.
     * Если ключ существует, его значение будет переопределено.
     * @param string $key новый ключ
     * @param string $value значение
     * @param integer $ttl время жизни кэша в секундах. 0 - никогда не истекает.
     * @return bool успех операции
     */
    public function set($key, $value, $ttl = 0);

    /**
     * Возврашает значение из кэша по ключу
     * @param string $key ключ
     * @return string значение из кэша, либо false, если ключ не существует или время жизни кэша истекло.
     */
    public function get($key);

    /**
     * Возвращает список значений указанных ключей
     * @param array $keys список ключей
     * @return array список значений из кэша
     */
    public function getList(array $keys);

    /**
     * Удаляет значение по ключу из кэша
     * @param string $key ключ
     * @return bool успех операции
     */
    public function remove($key);

    /**
     * Очищает весь кэш
     * @return bool успех операции
     */
    public function clear();

}
