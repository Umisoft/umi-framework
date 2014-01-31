<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\toolbox;

use Doctrine\DBAL\DriverManager;
use umi\dbal\builder\IQueryBuilderFactory;
use umi\dbal\cluster\IDbCluster;
use umi\dbal\cluster\IDbClusterAware;
use umi\dbal\cluster\server\IServer;
use umi\dbal\cluster\server\IServerFactory;
use umi\dbal\driver\IDialect;
use umi\dbal\exception\InvalidArgumentException;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты для работы с БД.
 */
class DbalTools implements IToolbox
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'db';
    /**
     * Тип соединения PDO к СУБД MySQL
     */
    const CONNECTION_TYPE_PDOMYSQL = 'pdo_mysql';
    /**
     * Тип соединения PDO к СУБД Sqlite
     */
    const CONNECTION_TYPE_PDOSQLITE = 'pdo_sqlite';

    use TToolbox;

    /**
     * @var array $servers конфигурация серверов, в формате:
     * <pre><code>
     * [
     *    [
     *        'id' => 'masterServer',
     *        'type' => 'master',
     *        'connection' => [
     *            'type' => DbTools::CONNECTION_TYPE_PDOMYSQL,
     *            'options' => [
     *                'dbname' => 'mydb',
     *                'user' => 'user',
     *                'password' => 'secret',
     *                'host' => 'localhost',
     *                'charset' => 'utf8', // for some drivers
     *                ...
     *            ]
     *        ]
     *    ],
     *    ...
     * ]
     * </code></pre>
     */
    public $servers = [];
    /**
     * @var string $dbClusterClass имя класса для создания кластера БД
     */
    public $dbClusterClass = 'umi\dbal\cluster\DbCluster';
    /**
     * @var string $serverFactoryClass имя класса для создания фабрики серверов кластера
     */
    public $serverFactoryClass = 'umi\dbal\toolbox\factory\ServerFactory';
    /**
     * @var string $queryBuilderFactoryClass имя класса для создания фабрики построителей запросов
     */
    public $queryBuilderFactoryClass = 'umi\dbal\toolbox\factory\QueryBuilderFactory';

    /**
     * @var array $dialectMap сопоставление SQL-диалектов драйверам соединений с БД
     */
    protected $dialectMap = [
        DbalTools::CONNECTION_TYPE_PDOMYSQL  => 'umi\dbal\driver\dialect\MySqlDialect',
        DbalTools::CONNECTION_TYPE_PDOSQLITE => 'umi\dbal\driver\dialect\SqliteDialect',
    ];

    /**
     * @var array $supportedConnectionTypes поддерживаемые типы соединений с БД
     */
    protected $supportedConnectionTypes = [
        DbalTools::CONNECTION_TYPE_PDOMYSQL,
        DbalTools::CONNECTION_TYPE_PDOSQLITE,
    ];

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'server',
            $this->serverFactoryClass,
            ['umi\dbal\cluster\server\IServerFactory']
        );
        $this->registerFactory(
            'queryBuilder',
            $this->queryBuilderFactoryClass,
            ['umi\dbal\builder\IQueryBuilderFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\dbal\cluster\IDbCluster':
                return $this->getCluster();
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
     * Возвращает кластер БД
     * @return IDbCluster
     */
    protected function getCluster()
    {

        $prototype = $this->getPrototype($this->dbClusterClass, ['umi\dbal\cluster\IDbCluster']);
        return $prototype->createSingleInstance(
            [],
            [],
            function (IDbCluster $dbCluster) {
                $this->servers = $this->configToArray($this->servers, true);
                foreach ($this->servers as $serverConfig) {
                    $dbCluster->addServer($this->configureServer($serverConfig));
                }
            }
        );
    }

    /**
     * Возвращает фабрику построителей запросов БД.
     * @return IQueryBuilderFactory
     */
    protected function getQueryBuilderFactory()
    {
        return $this->getFactory('queryBuilder');
    }

    /**
     * Возвращает фабрику серверов кластера.
     * @return IServerFactory
     */
    protected function getServerFactory()
    {
        return $this->getFactory('server', [$this->getQueryBuilderFactory()]);
    }

    /**
     * Создает и конфигурирует сервер
     * @param array|\Traversable $serverConfig конфигурация сервера
     * @throws InvalidArgumentException если конфигурация не валидна
     * @return IServer
     */
    protected function configureServer($serverConfig)
    {
        $this->validateServerConfig($serverConfig);

        list($connection, $dialect) = $this->configureConnection($serverConfig['connection']);

        $type = null;
        if (isset($serverConfig['type']) && !empty($serverConfig['type'])) {
            $type = $serverConfig['type'];
        }

        return $this
            ->getServerFactory()
            ->create($serverConfig['id'], $connection, $dialect, $type);
    }

    /**
     * Создает и конфигурирует драйвер БД
     * @param array $connectionConfig конфигурация драйвера
     * @throws InvalidArgumentException если конфигурация не валидна
     * @return array [IConnection, IDialect]
     */
    protected function configureConnection($connectionConfig)
    {
        $this->validateConnectionConfig($connectionConfig);

        $options = [];
        if (isset($connectionConfig['options'])) {
            $options = $connectionConfig['options'];
        }

        $options['driver'] = $connectionConfig['type'];

        $dialectClass = $this->dialectMap[$connectionConfig['type']];

        /** @var IDialect $dialect */
        $dialect = new $dialectClass;

        $options['platform'] = $dialect;

        $connection = DriverManager::getConnection($options);

        $dialect->initConnection($connection);

        return [
            $connection,
            $dialect
        ];
    }

    /**
     * Проверяет конфигурацию сервера
     * @param array $config
     * @throws InvalidArgumentException в случае неверной конфигурации
     */
    protected function validateServerConfig($config)
    {
        if (!is_array($config)) {
            throw new InvalidArgumentException($this->translate(
                'Server configuration should be an array or Traversable.'
            ));
        }
        if (!isset($config['id']) || empty($config['id'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find server id in configuration.'
            ));
        }
        if (!isset($config['connection']) || empty($config['connection'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find connection type in configuration.'
            ));
        }
    }

    /**
     * Проверяет конфигурацию соединения с бд, передаваемую Doctrine DriverManager
     * @param array $connectionConfig
     * @throws InvalidArgumentException
     */
    protected function validateConnectionConfig($connectionConfig)
    {
        if (!is_array($connectionConfig)) {
            throw new InvalidArgumentException($this->translate(
                'Db driver configuration should be an array or Traversable.'
            ));
        }

        if (!isset($connectionConfig['type'])) {
            throw new InvalidArgumentException($this->translate(
                'Cannot find connection type in configuration.'
            ));
        } else {
            if (!in_array($connectionConfig['type'], $this->supportedConnectionTypes)) {
                throw new InvalidArgumentException('Wrong connection type: ' . $connectionConfig['type']);
            }
        }

        if (isset($connectionConfig['options'])) {
            if (!(is_array($connectionConfig['options']))) {
                throw new InvalidArgumentException($this->translate(
                    'Db driver options should be an array or Traversable.'
                ));
            }
        }
    }
}
