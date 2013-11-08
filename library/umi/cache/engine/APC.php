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
 * Механизм хранения кэша Alternative PHP Cache.
 */
class APC implements ICacheEngine
{

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, $ttl = 0)
    {
        return apc_add($key, $value, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = 0)
    {
        return apc_store($key, $value, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return apc_fetch($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(array $keys)
    {
        $result = array_combine($keys, array_fill(0, count($keys), false));
        $result = array_merge($result, apc_fetch($keys));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return apc_delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $result = apc_clear_cache();
        $result &= apc_clear_cache('user');

        return (bool) $result;
    }
}