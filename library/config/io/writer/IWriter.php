<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\io\writer;

use umi\config\entity\IConfigSource;

/**
 * Интерфейс Writer'а конфигурации.
 */
interface IWriter
{
    /**
     * Записывает конфигурацию.
     * @param IConfigSource $config конфигурация
     * @return void
     */
    public function write(IConfigSource $config);
}