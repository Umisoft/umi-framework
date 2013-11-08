<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\toolbox\factory;

use umi\dbal\builder\IQueryBuilderFactory;
use umi\dbal\cluster\server\IServerFactory;
use umi\dbal\driver\IDbDriver;
use umi\dbal\exception\RuntimeException;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для создания серверов.
 */
class ServerFactory implements IServerFactory, IFactory
{

    use TFactory;

    /**
     * @var array $types классы для реализации серверов определенных типов
     */
    public $types = array(
        'master' => 'umi\dbal\cluster\server\MasterServer',
        'slave'  => 'umi\dbal\cluster\server\SlaveServer',
        'shard'  => 'umi\dbal\cluster\server\ShardServer',
    );
    /**
     * @var string $defaultType тип сервера по умолчанию
     */
    public $defaultType = 'master';

    /**
     * @var IQueryBuilderFactory $queryBuilderFactory фабрика построителей запросов
     */
    protected $queryBuilderFactory;

    /**
     * Конструктор
     * @param IQueryBuilderFactory $queryBuilderFactory фабрика построителей запросов
     */
    public function __construct(IQueryBuilderFactory $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create($serverId, IDbDriver $driver, $serverType = null)
    {
        if (is_null($serverType)) {
            $serverType = $this->defaultType;
        }
        if (!isset($this->types[$serverType])) {
            throw new RuntimeException($this->translate(
                'Cannot create server. Unknown server type "{type}".',
                ['type' => $serverType]
            ));
        }

        return $this->createInstance(
            $this->types[$serverType],
            [$serverId, $driver, $this->queryBuilderFactory],
            ['umi\dbal\cluster\server\IServer']
        );
    }
}
