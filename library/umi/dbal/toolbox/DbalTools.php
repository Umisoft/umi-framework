<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\toolbox;

use Traversable;
use umi\dbal\builder\IQueryBuilderFactory;
use umi\dbal\cluster\IDbCluster;
use umi\dbal\cluster\IDbClusterAware;
use umi\dbal\cluster\server\IServer;
use umi\dbal\cluster\server\IServerFactory;
use umi\dbal\driver\IDbDriver;
use umi\dbal\driver\IDbDriverFactory;
use umi\dbal\exception\InvalidArgumentException;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты для работы с БД.
 */
class DbalTools implements IDbalTools
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'db';

    use TToolbox;

    /**
     * @var array|Traversable $servers конфигурация серверов, в формате:
     * [
     *    [
     *        'id' => 'masterServer',
     *        'type' => 'master',
     *        'driver' => [
     *            'type' => 'mysql',
     *            'options' => [
     *                'dsn' => 'mysql:dbname=test;host=localhost',
     *                'user' => 'noname',
     *                'password' => 'password',
     *                ...
     *            ]
     *        ]
     *    ],
     *    ...
     * ]
     */
    public $servers = [];
    /**
     * @var string $dbClusterClass имя класса для создания кластера БД
     */
    public $dbClusterClass = 'umi\dbal\cluster\DbCluster';
    /**
     * @var string $dbDriverFactoryClass имя класса для создания фабрики драйверов БД
     */
    public $dbDriverFactoryClass = 'umi\dbal\toolbox\factory\DbDriverFactory';
    /**
     * @var string $serverFactoryClass имя класса для создания фабрики серверов кластера
     */
    public $serverFactoryClass = 'umi\dbal\toolbox\factory\ServerFactory';
    /**
     * @var string $queryBuilderFactoryClass имя класса для создания фабрики построителей запросов
     */
    public $queryBuilderFactoryClass = 'umi\dbal\toolbox\factory\QueryBuilderFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'serverFactory',
            $this->serverFactoryClass,
            ['umi\dbal\cluster\server\IServerFactory']
        );
        $this->registerFactory(
            'queryBuilderFactory',
            $this->queryBuilderFactoryClass,
            ['umi\dbal\builder\IQueryBuilderFactory']
        );
        $this->registerFactory(
            'dbDriverFactory',
            $this->dbDriverFactoryClass,
            ['umi\dbal\driver\IDbDriverFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\dbal\cluster\IDbCluster':
            {
                return $this->getCluster();
            }
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
        if ($object instanceof IDbClusterAware) {
            $object->setDbCluster($this->getCluster());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCluster()
    {
        if (null != ($instance = $this->getSingleInstance($this->dbClusterClass))) {
            return $instance;
        }
        /**
         * @var IDbCluster $dbCluster
         */
        $dbCluster = $this->createSingleInstance($this->dbClusterClass, [], ['umi\dbal\cluster\IDbCluster']);
        if ($this->servers instanceof Traversable) {
            $this->servers = iterator_to_array($this->servers, true);
        }
        if (is_array($this->servers)) {
            foreach ($this->servers as $serverConfig) {
                $dbCluster->addServer($this->configureServer($serverConfig));
            }
        }

        return $dbCluster;
    }

    /**
     * Возвращает фабрику драйверов БД
     * @return IDbDriverFactory
     */
    protected function getDbDriverFactory()
    {
        return $this->getFactory('dbDriverFactory');
    }

    /**
     * Возвращает фабрику драйверов БД
     * @return IQueryBuilderFactory
     */
    protected function getQueryBuilderFactory()
    {
        return $this->getFactory('queryBuilderFactory');
    }

    /**
     * Возвращает фабрику серверов кластера
     * @return IServerFactory
     */
    protected function getServerFactory()
    {
        return $this->getFactory('serverFactory', [$this->getQueryBuilderFactory()]);
    }

    /**
     * Создает и конфигурирует сервер
     * @param array|Traversable $serverConfig конфигурация сервера
     * @throws InvalidArgumentException если конфигурация не валидна
     * @return IServer
     */
    protected function configureServer($serverConfig)
    {
        if ($serverConfig instanceof Traversable) {
            $serverConfig = iterator_to_array($serverConfig, true);
        }
        if (!is_array($serverConfig)) {
            throw new InvalidArgumentException($this->translate(
                'Server configuration should be an array or Traversable.'
            ));
        }
        if (!isset($serverConfig['id']) || empty($serverConfig['id'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find server id in configuration.'
            ));
        }
        if (!isset($serverConfig['driver']) || empty($serverConfig['driver'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find driver configuration.'
            ));
        }
        $dbDriver = $this->configureDbDriver($serverConfig['driver']);

        $type = null;
        if (isset($serverConfig['type']) && !empty($serverConfig['type'])) {
            $type = $serverConfig['type'];
        }

        return $this->getServerFactory()
            ->create($serverConfig['id'], $dbDriver, $type);
    }

    /**
     * Создает и конфигурирует драйвер БД
     * @param array|Traversable $driverConfig конфигурация драйвера
     * @throws InvalidArgumentException если конфигурация не валидна
     * @return IDbDriver
     */
    protected function configureDbDriver($driverConfig)
    {
        if ($driverConfig instanceof Traversable) {
            $driverConfig = iterator_to_array($driverConfig, true);
        }
        if (!is_array($driverConfig)) {
            throw new InvalidArgumentException($this->translate(
                'Db driver configuration should be an array or Traversable.'
            ));
        }
        if (!isset($driverConfig['type']) || empty($driverConfig['type'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find driver type in configuration.'
            ));
        }
        $options = [];
        if (isset($driverConfig['options'])) {
            $options = $driverConfig['options'];
            if ($options instanceof Traversable) {
                $options = iterator_to_array($options, true);
            }
            if (!is_array($options)) {
                throw new InvalidArgumentException($this->translate(
                    'Db driver options should be an array or Traversable.'
                ));
            }
        }

        return $this->getDbDriverFactory()
            ->create($driverConfig['type'], $options);
    }
}
