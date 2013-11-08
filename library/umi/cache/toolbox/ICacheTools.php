<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\cache\toolbox;

use umi\toolkit\toolbox\IToolbox;

/**
 * Набор инструментов кеширования.
 */
interface ICacheTools extends IToolbox
{

    /**
     * Короткий alias
     */
    const ALIAS = 'cache';

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
}
