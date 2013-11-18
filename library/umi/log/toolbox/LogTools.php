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
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов логирования.
 */
class LogTools implements IToolbox
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'log';

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
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\log\ILogger':
                return $this->getLogger();
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{name}" does not support service "{interface}".',
            ['name' => self::NAME, 'interface' => $serviceInterfaceName]
        ));
    }

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
     * Возвращает экземпляр логгера
     * @throws OutOfBoundsException если задан несуществующий тип логгера
     * @return ILogger
     */
    protected function getLogger()
    {
        if (!isset($this->loggerClasses[$this->type])) {
            throw new OutOfBoundsException($this->translate(
                'Logger type "{type}" does not exist.',
                ['type' => $this->type]
            ));
        }

        $prototype = $this->getPrototype($this->loggerClasses[$this->type], ['umi\log\ILogger']);

        return $prototype->createSingleInstance(
            [$this->options],
            [],
            function(ILogger $logger)
            {
                $logger
                    ->setMinLevel($this->minLevel)
                    ->setMessageFormat($this->messageFormat);
            }
        );
    }
}
