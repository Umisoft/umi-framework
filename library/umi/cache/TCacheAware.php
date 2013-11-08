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
 * Трейт для поддержки кеширования.
 */
trait TCacheAware
{

    /**
     * @var ICache $_cache компонент для кеширования
     */
    private $_cache;

    /**
     * Устанавливает компонент для кэширования.
     * @param ICache $cache
     * @return self
     */
    public function setCache(ICache $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Возвращает данные из кэша по ключу. <br />
     * Если кеш данных не валидный, то он будет перезаписан.
     * @param string $key уникальный ключ
     * @param callable $algorithm алгоритм - callable-функция, результат которой будет положен в кеш и возвращен в использующий компонент
     * @param integer $expiration время жизни кеша в секундах. 0 - никогда не истекает.
     * @param array $invalidationTags массив тегов инвалидации кеша
     * @return mixed
     */
    protected function cache($key, callable $algorithm, $expiration = 0, array $invalidationTags = null)
    {
        if ($this->_cache) {
            return $this->_cache->algorithm($key, $algorithm, $expiration, $invalidationTags);
        }

        return $algorithm();
    }

    /**
     * Инвалидирует кеш по тегам
     * @param array $tags список тегов
     * @param int|null $time метка времени Unix, когда теги стали невалидными. Если не указано, используется текущее время
     * @return self
     */
    protected function invalidateCache(array $tags, $time = null)
    {
        if ($this->_cache) {
            $this->_cache->invalidateTags($tags, $time);
        }

        return $this;
    }

}