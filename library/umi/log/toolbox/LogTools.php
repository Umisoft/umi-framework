<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\log\toolbox;

use umi\log\exception\OutOfBoundsException;
use umi\log\ILogger;
use umi\log\ILoggerAware;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов логирования.
 */
class LogTools implements ILogTools
{

    use TToolbox;

    /**
     * Типы поддерживаемых логгеров.
     */
    public $loggerClasses = [
        self::TYPE_NULL   => 'umi\log\type\NullLogger',
        self::TYPE_FILE   => 'umi\log\type\FileLogger',
        self::TYPE_OUTPUT => 'umi\log\type\OutputLogger'
    ];
    /**
     * @var string $logger логгер по умолчанию.
     */
    public $type = self::TYPE_NULL;
    /**
     * @var null $minLevel минимальный уровень для логгироания
     */
    public $minLevel = null;
    /**
     * @var string $messageFormat формат сообщений
     */
    public $messageFormat = '{date} | {level} | {message}';
    /**
     * @var array $options опции логгера
     */
    public $options = [];

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof ILoggerAware) {
            $object->setLogger($this->getLogger());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLogger()
    {
        if (!isset($this->loggerClasses[$this->type])) {
            throw new OutOfBoundsException($this->translate(
                'Logger type "{type}" does not exist.',
                ['type' => $this->type]
            ));
        }

        $loggerClass = $this->loggerClasses[$this->type];

        if (null !== ($logger = $this->getSingleInstance($loggerClass))) {
            return $logger;
        }

        /**
         * @var ILogger $logger
         */
        $logger = $this->createSingleInstance(
            $loggerClass,
            [$this->options],
            [
                'umi\log\ILogger'
            ]
        );

        return $logger
            ->setMinLevel($this->minLevel)
            ->setMessageFormat($this->messageFormat);
    }
}
