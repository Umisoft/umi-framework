<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\toolbox;

use umi\config\cache\IConfigCacheEngine;
use umi\config\cache\IConfigCacheEngineAware;
use umi\config\entity\factory\IConfigEntityFactory;
use umi\config\entity\factory\IConfigEntityFactoryAware;
use umi\config\exception\OutOfBoundsException;
use umi\config\io\IConfigAliasResolverAware;
use umi\config\io\IConfigIO;
use umi\config\io\IConfigIOAware;
use umi\config\io\reader\IReader;
use umi\config\io\writer\IWriter;
use umi\i18n\TLocalesAware;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для работы с конфигурацией.
 */
class ConfigTools implements IToolbox
{
    /**
     * Имя набора инструментов.
     */
    const NAME = 'config';

    use TToolbox;

    /**
     * Конфигурация на основе Php файлов.
     */
    const TYPE_PHP = 'php';
    /**
     * @var string $entityFactoryClass фабрика сущностей конфигурации
     */
    public $entityFactoryClass = 'umi\config\toolbox\factory\ConfigEntityFactory';
    /**
     * @var string $ioServiceClass сервис I/O конфигурации
     */
    public $ioServiceClass = 'umi\config\io\ConfigIO';
    /**
     * @var string $ioServiceClass сервис I/O конфигурации
     */
    public $cacheServiceClass = 'umi\config\cache\ConfigCacheEngine';
    /**
     * @var bool $hasCache использовать ли кэш?
     */
    public $hasCache = false;
    /**
     * @var array $cache настройки для механизма кэширования
     */
    public $cache = [];
    /**
     * @var array $aliases список зарегистрированных символических имен
     */
    public $aliases = [];
    /**
     * @var string $type установленный тип конфигурации для набора инструментов
     */
    public $type = self::TYPE_PHP;
    /**
     * @var array $readers список reader 'ов конфигурации
     */
    public $readers = [
        self::TYPE_PHP => 'umi\config\io\reader\PhpFileReader',
    ];
    /**
     * @var array $writers список writer 'ов конфигурации
     */
    public $writers = [
        self::TYPE_PHP => 'umi\config\io\writer\PhpFileWriter',
    ];

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'entity',
            $this->entityFactoryClass,
            ['umi\config\entity\factory\IConfigEntityFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\config\io\IConfigIO':
                return $this->getConfigIO();
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
        if ($object instanceof IConfigIOAware) {
            $object->setConfigIO($this->getConfigIO());
        } elseif ($object instanceof IConfigAliasResolverAware) {
            $object->setConfigIO($this->getConfigIO());
        }

        if ($object instanceof IConfigEntityFactoryAware) {
            $object->setConfigEntityFactory($this->getConfigEntityFactory());
        }

        if ($this->hasCache && $object instanceof IConfigCacheEngineAware) {
            $object->setConfigCacheEngine($this->getConfigCacheEngine());
        }
    }

    /**
     * Возвращает сервис ввода-вывода для конфигурации.
     * @return IConfigIO
     */
    protected function getConfigIO()
    {
        return $this->getPrototype(
            $this->ioServiceClass,
            ['umi\config\io\IConfigIO']
        )
            ->createSingleInstance([$this->getReader(), $this->getWriter(), $this->aliases]);
    }

    /**
     * Возвращает объект reader'а конфигурации.
     * @return IReader
     * @throws OutOfBoundsException если необходимый reader не доступен
     */
    protected function getReader()
    {
        if (!isset($this->readers[$this->type])) {
            throw new OutOfBoundsException($this->translate(
                'Reader "{type}" is not available.',
                ['type' => $this->type]
            ));
        }

        return $this->getPrototype($this->readers[$this->type], ['umi\config\io\reader\IReader'])->createSingleInstance();
    }

    /**
     * Возвращает объект writer'а конфигурации.
     * @throws OutOfBoundsException если необходимый writer не доступен
     * @return IWriter
     */
    protected function getWriter()
    {
        if (!isset($this->writers[$this->type])) {
            throw new OutOfBoundsException($this->translate(
                'Writer "{type}" is not available.',
                ['type' => $this->type]
            ));
        }

        return $this->getPrototype($this->writers[$this->type], ['umi\config\io\writer\IWriter'])->createSingleInstance();
    }

    /**
     * Возвращает фабрику сущностей конфигурации.
     * @return IConfigEntityFactory
     */
    protected function getConfigEntityFactory()
    {
        return $this->getFactory('entity');
    }

    /**
     * Возвращает сервис кэширования конфигурации.
     * @return IConfigCacheEngine
     */
    protected function getConfigCacheEngine()
    {
        return $this->getPrototype(
            $this->cacheServiceClass,
            ['umi\config\cache\IConfigCacheEngine']
        )
            ->createSingleInstance($this->cache);
    }
}