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
 * Механизм хранения кэша XCache.
 */
class XCache implements ICacheEngine
{

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, $ttl = 0)
    {
        if (!xcache_isset($key)) {
            return xcache_set($key, $value);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = 0)
    {
        return xcache_set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return xcache_get($key) ? : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return xcache_unset($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return false; //TODO xcache_clear_cache?
    }
}