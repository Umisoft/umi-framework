<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\log\toolbox;

use Psr\Log\LoggerInterface;
use umi\toolkit\toolbox\IToolbox;

/**
 * Набор инструментов логирования.
 */
interface ILogTools extends IToolbox
{
    /**
     * Короткий alias
     */
    const ALIAS = 'log';

    /**
     * Заглушка, используется в случае когда логгер выключен
     */
    const TYPE_NULL = 'null';
    /**
     * Логирование, основаное на файле
     */
    const TYPE_FILE = 'file';
    /**
     * Логирование в буффер вывода
     */
    const TYPE_OUTPUT = 'output';

    /**
     * Возвращает экземпляр логгера
     * @return LoggerInterface
     */
    public function getLogger();
}