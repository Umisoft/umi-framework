<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\log\type;

use Psr\Log\InvalidArgumentException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\log\ILogger;

/**
 * Абстрактный класс логирования.
 */
abstract class BaseLogger implements ILogger, ILocalizable
{

    use TLocalizable;

    /**
     * @var string $format формат сообщения
     */
    protected $format = '{date} | {level} | {message}';
    /**
     * @var string $minLevel минимальный уровень записи ошибок. Ошибки с уровнем ниже заданного логироваться не будут
     */
    protected $minLevel = null;
    /**
     * @var array $logLevels уровни логгирования
     */
    protected $logLevels = [
        self::LOG_DEBUG,
        self::LOG_INFO,
        self::LOG_NOTICE,
        self::LOG_WARNING,
        self::LOG_ERROR,
        self::LOG_CRITICAL,
        self::LOG_ALERT,
        self::LOG_EMERGENCY
    ];

    /**
     * {@inheritdoc}
     */
    public function setMinLevel($minLevel)
    {
        $this->minLevel = $minLevel;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Записывает лог.
     * @param string $level уровень
     * @param string $message сообщение
     * @throws InvalidArgumentException если уровень неверный
     * @return void
     */
    abstract protected function write($level, $message);

    /**
     * {@inheritdoc}
     */
    public function emergency($message, array $placeholders = [])
    {
        $this->log(self::LOG_EMERGENCY, $message, $placeholders);
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $placeholders = [])
    {
        $this->log(self::LOG_ALERT, $message, $placeholders);
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $placeholders = [])
    {
        $this->log(self::LOG_CRITICAL, $message, $placeholders);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $placeholders = [])
    {
        $this->log(self::LOG_ERROR, $message, $placeholders);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, array $placeholders = [])
    {
        $this->log(self::LOG_WARNING, $message, $placeholders);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $placeholders = [])
    {
        $this->log(self::LOG_NOTICE, $message, $placeholders);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $placeholders = [])
    {
        $this->log(self::LOG_INFO, $message, $placeholders);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $placeholders = [])
    {
        $this->log(self::LOG_DEBUG, $message, $placeholders);
    }

    /**
     * {@inheritdoc}
     */
    public final function log($level, $message, array $placeholders = [])
    {
        $levelIndex = array_search($level, $this->logLevels);
        if ($levelIndex === false) {
            throw new InvalidArgumentException(
                $this->translate(
                    'Log level "{level}" is not supported.',
                    ['level' => $level]
                ));
        }

        $minLevelIndex = array_search($this->minLevel, $this->logLevels);
        if ($minLevelIndex <= $levelIndex) {
            $message = $this->interpolate($message, $placeholders);

            $message = $this->interpolate(
                $this->format,
                [
                    'date'    => date('Y-m-d H:i:s'),
                    'level'   => $level,
                    'message' => $message
                ]
            );

            $this->write($level, $message);
        }
    }

    /**
     * Форматирует сообщение используя плейсхолдеры.
     * @param string $message сообщение
     * @param array $placeholders плейсхолдеры для сообщения
     * @return string отформатированное сообщение
     */
    protected function interpolate($message, array $placeholders = [])
    {
        if ($placeholders) {
            $replace = [];
            foreach ($placeholders as $key => $val) {
                $replace['{' . $key . '}'] = $val;
            }

            return strtr($message, $replace);
        } else {
            return $message;
        }
    }
}