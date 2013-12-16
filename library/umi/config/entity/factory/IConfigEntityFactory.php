<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\entity\factory;

use umi\config\entity\IConfigSource;
use umi\config\entity\ISeparateConfigSource;

/**
 * Интерфейс фабрики сущностей конфигурации.
 */
interface IConfigEntityFactory
{
    /**
     * Тип отдельной конфигурации - лениво загружаемая конфигурация.
     */
    const SEPARATE_LAZY = 'lazy';

    /**
     * Создает конфигурацию, на основе источника данных.
     * @param string $alias символическое имя конфигурации
     * @param array $source конфигурация
     * @return IConfigSource
     */
    public function createConfigSource($alias, array &$source);

    /**
     * Создает отдельную конфигурацию.
     * @param string $type тип отдельной конфигурации
     * @param string $alias символическое имя конфигурации
     * @return ISeparateConfigSource
     */
    public function createSeparateConfigSource($type, $alias);
}