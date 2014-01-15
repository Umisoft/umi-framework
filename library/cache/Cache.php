<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\cache;

use umi\cache\engine\ICacheEngine;

/**
 * Компонент для работы с кэшем.
 */
class Cache implements ICache
{

    /**
     * Префикс для хранения тэгов кэша
     */
    const TAG_PREFIX = '#';

    /**
     * @var ICacheEngine $engine используемый кэширующий механизм
     */
    protected $engine;

    /**
     * Конструктор
     * @param ICacheEngine $engine используемый кэширующий механизм
     */
    public function __construct(ICacheEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = 0)
    {
        $this->engine->set($key, serialize([$value, time()]), $ttl);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, array $invalidationTags = null)
    {
        if (!empty($invalidationTags)) {
            return $this->getTaggedCache($key, $invalidationTags);
        }

        $serialized = $this->engine->get($key);
        if (empty($serialized)) {
            return null;
        }

        $cache = @unserialize($serialized);
        $value = isset($cache[0]) ? $cache[0] : null;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return $this->engine->remove($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->engine->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateTags(array $tags, $time = null)
    {
        $time = $time ? : time();
        foreach ($tags as $tag) {
            $this->engine->set(self::TAG_PREFIX . $tag, $time);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function algorithm($key, callable $algorithm, $ttl = 0, array $invalidationTags = null)
    {
        if (!$result = $this->get($key, $invalidationTags)) {
            $result = call_user_func($algorithm);
            $this->set($key, $result, $ttl);
        }

        return $result;
    }

    /**
     * Возвращает значение из кэша по ключу, проверяя теги инвалидации
     * @param string $key ключ
     * @param array $invalidationTags список тегов инвалидации
     * @return mixed null, если кэш не валиден
     */
    protected function getTaggedCache($key, array $invalidationTags)
    {
        $keys = [$key];
        foreach ($invalidationTags as $tag) {
            $keys[] = self::TAG_PREFIX . $tag;
        }
        $taggedCache = $this->engine->getList($keys);

        $serialized = $taggedCache[$key];
        if (empty($serialized)) {
            return null;
        }

        $cache = @unserialize($serialized);
        if (!isset($cache[0])) {
            return null;
        }

        $value = $cache[0];
        $valueTime = isset($cache[1]) ? $cache[1] : 0;
        unset($taggedCache[$key]);

        if (in_array(false, $taggedCache, true)) {
            foreach ($taggedCache as $tagName => $tagTime) {
                if (!$tagTime) {
                    $this->engine->set($tagName, time() - 1);
                }
                if (!$tagTime || $tagTime >= $valueTime) {
                    $value = null;
                }
            }
        } else {
            foreach ($taggedCache as $tagTime) {
                if (!$tagTime || $tagTime >= $valueTime) {
                    return null;
                }
            }
        }

        return $value;
    }

}
