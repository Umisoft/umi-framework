<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\cache;

/**
 * Компонент для работы с кэшем.
 */
interface ICache
{

    /**
     * Записывает в кэш значение по ключу
     * @param string $key ключ
     * @param mixed $value значение
     * @param int $ttl время жизни кэша в секундах. 0 - никогда не истекает
     * @return self
     */
    public function set($key, $value, $ttl = 0);

    /**
     * Возвращает значение из кэша по ключу
     * @param string $key ключ
     * @param array $invalidationTags массив тегов инвалидации кэша
     * @return mixed значение, либо null
     */
    public function get($key, array $invalidationTags = null);

    /**
     * Удаляет значение из кэша по ключу
     * @param string $key ключ
     * bool успех операции
     */
    public function remove($key);

    /**
     * Очищает весь кэш
     * bool успех операции
     */
    public function clear();

    /**
     * Инвалидирует кеш по тегам
     * @param array $tags список тегов
     * @param int|null $time метка времени Unix, когда теги стали невалидными. Если не указано, используется текущее время
     * @return self
     */
    public function invalidateTags(array $tags, $time = null);

    /**
     * Кэширует и возвращает результат работы какого-либо алгоритма.
     * Если кэш данных не валидный, то алгоритм будет перезапущен и кэш будет перезаписан.
     * @param string $key уникальный ключ
     * @param callable $algorithm алгоритм - callable-функция, результат которой будет положен в кэш и возвращен в использующий компонент
     * @param int $ttl время жизни кэша в секундах. 0 - никогда не истекает
     * @param array|null $invalidationTags массив тегов инвалидации кэша
     * @return mixed
     */
    public function algorithm($key, callable $algorithm, $ttl = 0, array $invalidationTags = null);

}
