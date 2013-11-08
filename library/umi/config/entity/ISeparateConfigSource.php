<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\entity;

/**
 * Отделенная конфигурация, загружаемая при необходимости.
 * С помощью данного типа конфигурации реализуется Lazy конфигурационные файлы,
 * а также подключаемые удаленные конфигурации и т.п.
 */
interface ISeparateConfigSource extends IConfigSource
{
    /**
     * Возвращает
     * @return IConfigSource
     */
    public function getSeparateConfig();
}