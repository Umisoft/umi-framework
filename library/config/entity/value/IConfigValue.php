<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\entity\value;

/**
 * Хранит в себе и предоставляет доступ к тройке значений
 * конфигурации: сессионое, локально и основное значение.
 */
interface IConfigValue
{
    /**
     * Автоматически определить тип значения.
     */
    const KEY_AUTO = null;
    /**
     * Локальное значение.
     */
    const KEY_LOCAL = 0x01;
    /**
     * Мастер значение.
     */
    const KEY_MASTER = 0x02;

    /**
     * Возвращает значение указанного типа. Если указан тип
     * IConfig::KEY_AUTO, то будет выбрано наиболее подходящее значение.
     * @param int $type тип значения
     * @return mixed значение
     */
    public function get($type = self::KEY_AUTO);

    /**
     * Устанавливает сессионое значение конфигурации.
     * @param mixed $value значение
     * @param int $type
     * @return self
     */
    public function set($value, $type = self::KEY_AUTO);

    /**
     * Проверяет существование значения заданного типа. Для типа
     * IConfig::KEY_AUTO проверяет наличие какого-либо значения.
     * @param int $type тип значения
     * @return bool существует ли значение
     */
    public function has($type = self::KEY_AUTO);

    /**
     * Удаляет сессионное значение конфигурации.
     * При удалении, сессионное значение сбрасывается и выставляется флаг об удалении. Таким образом,
     * методы получения автоматического значения, возвращают результаты для master значения. Но при
     * явном запросе локального значения, оно будет получено.
     * Флаг удаления сбрасывается при установке нового сессионного значения, либо его сбросе.
     */
    public function del($type = self::KEY_AUTO);

    /**
     * Сбрасывает сессионное значение.
     * Сессионное значение удаляется, также сбрасывается флаг удаленного значения.
     */
    public function reset();

    /**
     * Сохраняет сессионные изменения в конфигурацию.
     * @return self
     */
    public function save();
}