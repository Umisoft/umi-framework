<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\cache;

/**
 * Интерфейс для внедрения поддержки кэширования конфигурации.
 * @internal
 */
interface IConfigCacheEngineAware
{
    /**
     * Устанавливает сервис кэширования конфигурации.
     * @param IConfigCacheEngine $cacheEngine сервис
     */
    public function setConfigCacheEngine(IConfigCacheEngine $cacheEngine);
}