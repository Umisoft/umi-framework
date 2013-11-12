<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\toolbox;

use umi\config\entity\factory\IConfigEntityFactory;
use umi\config\exception\OutOfBoundsException;
use umi\config\io\IConfigIO;
use umi\config\io\reader\IReader;
use umi\config\io\writer\IWriter;
use umi\toolkit\toolbox\IToolbox;

/**
 * Инструменты для работы с конфигурацией.
 */
interface IConfigTools extends IToolbox
{
    /**
     * Возвращает фабрику сущностей конфигурации.
     * @return IConfigEntityFactory
     */
    public function getConfigEntityFactory();

    /**
     * Возвращает сервис ввода-вывода для конфигурации.
     * @return IConfigIO
     */
    public function getConfigIO();

    /**
     * Возвращает сервис кэширования конфигурации.
     * @return mixed
     */
    public function getConfigCacheEngine();

    /**
     * Возвращает объект reader'а конфигурации.
     * @return IReader
     * @throws OutOfBoundsException если необходимый reader не доступен
     */
    public function getReader();

    /**
     * Возвращает объект writer'а конфигурации.
     * @return IWriter
     * @throws OutOfBoundsException если необходимый writer не доступен
     */
    public function getWriter();
}