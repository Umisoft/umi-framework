<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\toolbox\factory;

use umi\dbal\driver\IDbDriver;
use umi\dbal\driver\IDbDriverFactory;
use umi\dbal\driver\ITableFactory;
use umi\dbal\exception\NotAvailableDriverException;
use umi\dbal\exception\RuntimeException;
use umi\i18n\TLocalizable;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика драйверов БД.
 */
class DbDriverFactory implements IDbDriverFactory, IFactory
{

    use TFactory;

    public $tableFactoryClass = 'umi\dbal\toolbox\factory\TableFactory';

    /**
     * @var string[] $types типы поддерживаемых драйверов
     */
    public $types = [
        'mysql' => 'umi\dbal\driver\mysql\MySqlDriver',
        'sqlite' => 'umi\dbal\driver\sqlite\SqliteDriver'
    ];

    /**
     * @var array|\Traversable $factories конфигурация фабрик, пробрасывается в конкретную фабрику автоматически
     */
    public $factories = [
        'mysql'  => [
            'tableSchemeClass'      => 'umi\dbal\driver\mysql\MySqlTable',
            'columnSchemeClass'     => 'umi\dbal\driver\ColumnScheme',
            'constraintSchemeClass' => 'umi\dbal\driver\ConstraintScheme',
            'indexSchemeClass'      => 'umi\dbal\driver\IndexScheme'
        ],
        'sqlite' => [
            'tableSchemeClass'      => 'umi\dbal\driver\sqlite\SqliteTable',
            'columnSchemeClass'     => 'umi\dbal\driver\ColumnScheme',
            'constraintSchemeClass' => 'umi\dbal\driver\ConstraintScheme',
            'indexSchemeClass'      => 'umi\dbal\driver\sqlite\SqliteIndex'
        ]
    ];

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'mysql',
            $this->tableFactoryClass,
            ['umi\dbal\driver\ITableFactory']
        );

        $this->registerFactory(
            'sqlite',
            $this->tableFactoryClass,
            ['umi\dbal\driver\ITableFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function create($type, array $options = [])
    {
        if (!isset($this->types[$type])) {
            throw new RuntimeException($this->translate(
                'Unknown database driver {type}".',
                ['type' => $type]
            ));
        }

        $driverPrototype = $this->getPrototype(
            $this->types[$type],
            ['umi\dbal\driver\IDbDriver']
        );
        /**
         * @var IDbDriver $driver
         */
        $driver = $driverPrototype->createInstance(
            [$this->createTableFactory($type)],
            $options
        );

        if (!$driver->isAvailable()) {
            throw new NotAvailableDriverException($this->translate(
                'PDO driver "{type}" in not supported.',
                ['type' => $type]
            ));
        }

        return $driver;
    }

    /**
     * @param string $type тип драйвера
     * @return ITableFactory
     */
    protected function createTableFactory($type)
    {
        return $this->createFactory($type);
    }
}
