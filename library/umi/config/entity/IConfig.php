<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\entity;

use umi\spl\container\IContainer;

/**
 * Интерфейс контейнера конфигурации.
 */
interface IConfig extends IContainer, \ArrayAccess, \Countable, \Iterator
{
    /**
     * Разделитель ключей в пути.
     */
    const PATH_SEPARATOR = '/';

    /**
     * Получает значение по заданному пути.
     * @param string $path путь
     * @return mixed|IConfig
     */
    public function get($path);

    /**
     * Устанавливает значение по заданному пути.
     * @param string $path путь
     * @param mixed $value значение
     * @return self
     */
    public function set($path, $value);

    /**
     * Преобразует конфигурацию в массив значений.
     * @return array
     */
    public function toArray();

    /**
     * Устанавливает значения в конфиг, указанные в массиве.
     * @param array $source массив значений конфигурации.
     * @return self
     */
    public function merge(array $source);

    /**
     * Проверяет, существует ли значение по заданному пути.
     * @param string $path путь.
     * @return bool
     */
    public function has($path);

    /**
     * Удаляет значение из конфигурации по заданному пути.
     * @param string $path путь
     * @return self
     */
    public function del($path);

    /**
     * Сбрасывает сессионное значение по заданному пути. Если путь не указан, то
     * будут сброшены значения всего конфига.
     * @param null|string $path путь
     * @return self
     */
    public function reset($path = null);
}