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
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов кеширования.
 */
class CacheTools implements ICacheTools
{

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
        return $this->createSingleInstance(
            $this->cacheServiceClass,
            [$this->getCacheEngine()],
            ['umi\cache\ICache']
        );
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

        return $this->createSingleInstance(
            $this->cacheEngineClasses[$this->type],
            [$this->options],
            ['umi\cache\engine\ICacheEngine']
        );
    }
}
