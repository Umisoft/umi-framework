<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\cache;

use umi\config\entity\IConfigSource;
use umi\config\exception\RequiredDependencyException;
use umi\config\exception\RuntimeException;

/**
 * Трейт для внедрения поддержки кэширования конфигурации.
 * @internal
 */
trait TConfigCacheEngineAware
{
    /**
     * @var IConfigCacheEngine $_configCacheEngine
     */
    private $_configCacheEngine;

    /**
     * Устанавливает сервис кэширования конфигурации.
     * @param IConfigCacheEngine $cacheEngine сервис
     */
    public final function setConfigCacheEngine(IConfigCacheEngine $cacheEngine)
    {
        $this->_configCacheEngine = $cacheEngine;
    }

    /**
     * Проверяет, установлен ли сервис кэширования конфигурации.
     * @return bool
     */
    protected final function hasConfigCacheEngine()
    {
        return $this->_configCacheEngine != null;
    }

    /**
     * Проверяет, актуален ли кэш конфигурации.
     * @param string $alias символическое имя конфигурации
     * @param int $timestamp время "актуальности"
     * @return bool
     */
    protected final function isConfigCacheActual($alias, $timestamp)
    {
        return $this->getConfigCacheEngine()
            ->isActual($alias, $timestamp);
    }

    /**
     * Загружает конфигурацию из кэша по ее символическому имени.
     * @param string $alias символическое имя конфигурации
     * @throws RuntimeException если заданная конфигурация не найдена в кэше
     * @return IConfigSource
     */
    protected final function loadConfig($alias)
    {
        return $this->getConfigCacheEngine()
            ->load($alias);
    }

    /**
     * Сохраняет конфигурацию в кэш.
     * @param IConfigSource $config конфигурация
     * @return self
     */
    protected final function saveConfig(IConfigSource $config)
    {
        $this->getConfigCacheEngine()
            ->save($config);

        return $this;
    }

    /**
     * Возвращает сервис для кэширования.
     * @throws RequiredDependencyException если сервис кэширования не внедрен
     * @return IConfigCacheEngine
     */
    private final function getConfigCacheEngine()
    {
        if (!$this->_configCacheEngine) {
            throw new RequiredDependencyException(sprintf(
                'Config cache engine service is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_configCacheEngine;
    }

}