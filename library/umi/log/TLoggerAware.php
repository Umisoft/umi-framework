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
 * Трейт для поддержки логирования.
 */
trait TLoggerAware
{
    /**
     * @var LoggerInterface $_logger логгер
     */
    private $_logger;

    /**
     * Устанавливает логгер.
     * @param LoggerInterface $logger логгер
     */
    public final function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * Записывает сообщение в лог.
     * @param string $level уровень критичности сообщения
     * @param string $message сообщение, поддерживает плейсхолдеры в формате {placeholder}
     * @param array $placeholders список плейсхолдеров
     * @return $this
     */
    protected function log($level, $message, array $placeholders = [])
    {
        if ($this->_logger) {
            $this->_logger->log($level, $message, $placeholders);
        }

        return $this;
    }

    /**
     * Pаписывает сообщение для отладки в лог (level = LOG_DEBUG)
     * @param string $message сообщение, поддерживает плейсхолдеры в формате {placeholder}
     * @param array $placeholders список плейсхолдеров
     * @return $this
     */
    protected final function trace($message, array $placeholders = [])
    {
        return $this->log(ILogger::LOG_DEBUG, $message, $placeholders);
    }
}
