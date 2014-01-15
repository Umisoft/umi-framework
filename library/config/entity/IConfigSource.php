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
 * Интерфейс контейнера конфигурации, с.
 */
interface IConfigSource extends IConfig
{
    /**
     * Вовзвращает символическое имя.
     * @return string
     */
    public function getAlias();

    /**
     * Возвращает все значения конфигурации в виде массива.
     * @internal
     * @return array
     */
    public function getSource();
}