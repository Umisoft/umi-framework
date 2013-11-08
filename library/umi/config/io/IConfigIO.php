<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\io;

use umi\config\entity\IConfigSource;
use umi\config\exception\RuntimeException;

/**
 * Интерфейс Input/Output для операций конфигурации.
 */
interface IConfigIO
{
    /**
     * Регистрирует символическое имя для мастер/локал директорий.
     * @param string $alias символическое имя
     * @param string $masterDirectory мастер директория
     * @param string|null $localDirectory локальная директория
     * @return self
     */
    public function registerAlias($alias, $masterDirectory, $localDirectory = null);

    /**
     * Возвращает имена локального и мастер файла конфигурации
     * для заданного имени.
     * @param string $alias имя конфигурации
     * @throws RuntimeException если не найдено соответсвие символического имени
     * @return array
     */
    public function getFilesByAlias($alias);

    /**
     * Читает конфигурацию с заданным символическим именем.
     * @param string $alias символическое имя
     * @return IConfigSource
     */
    public function read($alias);

    /**
     * Записывает конфигурацию.
     * @param IConfigSource $config
     * @return self
     */
    public function write(IConfigSource $config);
}