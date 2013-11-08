<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\log;

use Psr\Log\LoggerInterface;

/**
 * Интерфейс логгера.
 */
interface ILogger extends LoggerInterface
{
    /**
     * Авария.
     */
    const LOG_EMERGENCY = 'emergency';
    /**
     * Тревога.
     */
    const LOG_ALERT = 'alert';
    /**
     * Критическая ошибка.
     */
    const LOG_CRITICAL = 'critical';
    /**
     * Ошибка.
     */
    const LOG_ERROR = 'error';
    /**
     * Предупреждение.
     */
    const LOG_WARNING = 'warning';
    /**
     * Замечание.
     */
    const LOG_NOTICE = 'notice';
    /**
     * Информация.
     */
    const LOG_INFO = 'info';
    /**
     * Отладочная информация.
     */
    const LOG_DEBUG = 'debug';

    /**
     * Устанавливает минимальный уровень логгирования.
     * @param int $minLevel минимальный уровень логгирования
     * @return self
     */
    public function setMinLevel($minLevel);

    /**
     * Устанавливает формат сообщения.
     * @param string $format формат сообщения
     * @return self
     */
    public function setMessageFormat($format);

}