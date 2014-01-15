<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\cache\toolbox;

use umi\cache\engine\ICacheEngine;
use umi\cache\exception\OutOfBoundsException;
use umi\cache\ICache;
use umi\cache\ICacheAware;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов кеширования.
 */
class CacheTools implements IToolbox
{
    /**
     * Имя набора инструмента
     */
    const NAME = 'cache';

    /**
     * Кеширование с помощью APC
     */
    const TYPE_APC = 'apc';
    /**
     * Кеширование в простой таблице БД
     */
    const TYPE_DB = 'db';
    /**
     * Кеширование с помощью Memcached
     */
    const TYPE_MEMCACHED = 'memcached';
    /**
     * Кеширование с помощью XCache
     */
    const TYPE_XCACHE = 'xcache';

    use TToolbox;

    /**
     * Типы поддерживаемых кеширующих механизмов.
     */
    public $cacheEngineClasses = [
        self::TYPE_APC       => 'umi\cache\engine\APC',
        self::TYPE_DB        => 'umi\cache\engine\Db',
        self::TYPE_MEMCACHED => 'umi\cache\engine\Memcached',
        self::TYPE_XCACHE    => 'umi\cache\engine\XCache'
    ];
    /**
     * @var string $cacheServiceClass класс для создания сервиса кеширования
     */
    public $cacheServiceClass = 'umi\cache\Cache';
    /**
     * @var string $type тип используемого кеширующего механизма
     */
    public $type;
    /**
     * @var array $options опции кеширующего механизма
     */
    public $options = [];

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($this->type && $object instanceof ICacheAware) {
            $object->setCache($this->getCache());
        }
    }

    /**
     * Возвращает сервис для кеширования
     * @return ICache
     */
    protected function getCache()
    {
        return $this->getPrototype(
            $this->cacheServiceClass,
            ['umi\cache\ICache']
        )
            ->createSingleInstance([$this->getCacheEngine()]);
    }

    /**
     * Возвращает кеширующий механизм
     * @throws OutOfBoundsException
     * @return ICacheEngine
     */
    protected function getCacheEngine()
    {
        if (!isset($this->cacheEngineClasses[$this->type])) {
            throw new OutOfBoundsException($this->translate(
                'Cache engine type "{type}" does not exist.',
                ['type' => $this->type]
            ));
        }

        $options = $this->configToArray($this->options, true);

        return $this->getPrototype(
            $this->cacheEngineClasses[$this->type],
            ['umi\cache\engine\ICacheEngine']
        )
            ->createSingleInstance([$options]);
    }
}
