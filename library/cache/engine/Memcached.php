<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\cache\engine;

use umi\cache\exception\InvalidArgumentException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Механизм хранения кэша Memcached.
 */
class Memcached implements ICacheEngine, ILocalizable
{

    use TLocalizable;

    /**
     * @var \Memcached $memcached представление соединений к memcached-серверам
     */
    private $memcached;

    /**
     * Конструктор.
     * @param array $servers список опций в формате [$host => ['port' => $port, 'weight' => $weight], ...]
     * @throws InvalidArgumentException при неверных опциях
     */
    public function __construct(array $servers = [])
    {
        $this->memcached = new \Memcached();

        foreach ($servers as $host => $options) {
            $this->addServer($host, $options);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, $ttl = 0)
    {
        $expiration = $ttl > 0 ? $ttl + time() : 0;

        return $this->memcached->add($key, $value, $expiration);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = 0)
    {
        $expiration = $ttl > 0 ? $ttl + time() : 0;

        return $this->memcached->set($key, $value, $expiration);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->memcached->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(array $keys)
    {
        $result = array_combine($keys, array_fill(0, count($keys), false));
        $result = array_merge($result, $this->memcached->getMulti($keys));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return $this->memcached->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->memcached->flush();
    }

    /**
     * Добавляет memcached-сервер в пул соединений.
     * @param string $host имя хоста memcached-сервера
     * @param array $options список опций в формате ['port' => $port, 'weight' => $weight]
     * @throws InvalidArgumentException в случае некорректно заданных опций
     */
    protected function addServer($host, array $options = [])
    {
        $port = isset($options['port']) && !empty($options['port']) ? $options['port'] : 11211;
        $weight = isset($options['weight']) && !empty($options['weight']) ? $options['weight'] : 0;

        $this->memcached->addServer($host, $port, $weight);
    }
}
