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
use umi\config\exception\RuntimeException;

/**
 * Интерфейс для кэширования конфигурационных файлов.
 */
interface IConfigCacheEngine
{
    /**
     * Проверяет, актуален ли кэш конфигурации.
     * @param string $alias символическое имя конфигурации
     * @param int $timestamp время "актуальности"
     * @return bool
     */
    public function isActual($alias, $timestamp);

    /**
     * Загружает конфигурацию из кэша по ее символическому имени.
     * @param string $alias символическое имя конфигурации
     * @throws RuntimeException если заданная конфигурация не найдена в кэше
     * @return IConfigSource
     */
    public function load($alias);

    /**
     * Сохраняет конфигурацию в кэш.
     * @param IConfigSource $config конфигурация
     * @return self
     */
    public function save(IConfigSource $config);
}