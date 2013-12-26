<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\toolbox;

use umi\dbal\toolbox\DbalTools;
use utest\dbal\DbalTestCase;

/**
 * Тестирование инструментария для работы с базами данных
 */
class DbalToolsTest extends DbalTestCase
{
    /**
     * @var DbalTools
     */
    protected $dbal;

    protected function setUpFixtures()
    {
        $this->dbal = new DbalTools();
        $this->resolveOptionalDependencies($this->dbal);
    }

    public function testGetService()
    {
        $dbCluster = $this->dbal->getService('umi\dbal\cluster\IDbCluster', null);
        $this->assertInstanceOf('umi\dbal\cluster\IDbCluster', $dbCluster);
        $this->assertTrue($dbCluster === $this->dbal->getService('umi\dbal\cluster\IDbCluster', null));
    }

    public function testDbalToolsServerConfig1()
    {
        $this->dbal->servers = ['wrongServerConfig' => 'wrongServerConfig'];
        $e = null;
        try {
            $this->dbal->getService('umi\dbal\cluster\IDbCluster', null);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Server configuration should be an array or Traversable.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function testDbalToolsServerConfig2()
    {
        $this->dbal->servers = ['wrongServerConfig' => []];
        $e = null;
        try {
            $this->dbal->getService('umi\dbal\cluster\IDbCluster', null);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Cannot find server id in configuration.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function testDbalToolsServerConfig3()
    {
        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'   => 'wrongServerId',
                'type' => 'master'
            ]
        ];
        $e = null;
        try {
            $this->dbal->getService('umi\dbal\cluster\IDbCluster', null);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Cannot find connection type in configuration.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function testDbalToolsServerConfig4()
    {
        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'         => 'wrongServerId',
                'type'       => 'master',
                'connection' => 'wrongDriverConfig'
            ]
        ];
        $e = null;
        try {
            $this->dbal->getService('umi\dbal\cluster\IDbCluster', null);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Db driver configuration should be an array or Traversable.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function testDbalToolsServerConfig5()
    {
        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'     => 'wrongServerId',
                'type'   => 'master',
                'driver' => [
                    'type' => ''
                ]
            ]
        ];
        $e = null;
        try {
            $this->dbal->getService('umi\dbal\cluster\IDbCluster', null);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Cannot find connection type in configuration.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function testDbalToolsServerConfig6()
    {
        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'         => 'wrongServerId',
                'type'       => 'master',
                'connection' => [
                    'type'    => 'pdo_mysql',
                    'options' => 'WrongOptionsValue'
                ]
            ]
        ];
        $e = null;
        try {
            $this->dbal->getService('umi\dbal\cluster\IDbCluster', null);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Db driver options should be an array or Traversable.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function testDbalToolsServerConfig7()
    {
        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'         => 'wrongServerId',
                'type'       => 'master',
                'connection' => [
                    'type'    => 'pdo_mysql',
                    'options' => ['charset' => 'utf8']
                ]
            ]
        ];

        $this->assertInstanceOf(
            'umi\dbal\cluster\IDbCluster',
            $this->dbal->getService('umi\dbal\cluster\IDbCluster', null),
            'Ожидается, что IDbTools может вернуть сервис IDbCluster'
        );
    }

    /**
     * @expectedException \umi\dbal\exception\InvalidArgumentException
     */
    public function testServerConfigWrongType()
    {

        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'         => 'wrongServerId',
                'type'       => 'master',
                'connection' => [
                    'type'    => 'pdo_dooodoooooo',
                    'options' => ['charset' => 'utf8']
                ]
            ]
        ];
        $this->dbal->getService('umi\dbal\cluster\IDbCluster', null);
    }

    public function testSqliteOptions()
    {
        $this->dbal->servers = [
            [
                'id'         => 'sqliteMem',
                'type'       => 'master',
                'connection' => [
                    'type'    => DbalTools::CONNECTION_TYPE_PDOSQLITE,
                    'options' => ['memory' => true]
                ]
            ],
            [
                'id'         => 'sqliteFile',
                'type'       => 'master',
                'connection' => [
                    'type'    => DbalTools::CONNECTION_TYPE_PDOSQLITE,
                    'options' => ['path' => __DIR__ . '/../data/test.db']
                ]
            ],
        ];
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IServer',
            $this->dbal
                ->getService('umi\dbal\cluster\IDbCluster', null)
                ->getServer('sqliteMem'),
            'Sqlite memory driver must be available'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IServer',
            $this->dbal
                ->getService('umi\dbal\cluster\IDbCluster', null)
                ->getServer('sqliteFile'),
            'Sqlite memory driver must be available'
        );
    }

    public function testSqliteEmptyOptions()
    {
        $this->dbal->servers = [
            [
                'id'         => 'sqliteMem',
                'type'       => 'master',
                'connection' => [
                    'type'    => DbalTools::CONNECTION_TYPE_PDOSQLITE,
                    'options' => ['memory' => true]
                ]
            ],
            [
                'id'         => 'sqliteFile',
                'type'       => 'master',
                'connection' => [
                    'type'    => DbalTools::CONNECTION_TYPE_PDOSQLITE,
                    'options' => ['path' => __DIR__ . '/../data/test.db']
                ]
            ],
            [
                'id'         => 'sqliteEmptyConfig',
                'type'       => 'master',
                'connection' => [
                    'type'    => DbalTools::CONNECTION_TYPE_PDOSQLITE,
                    'options' => [] // empty config will fail
                ]
            ]
        ];
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IServer',
            $this->dbal
                ->getService('umi\dbal\cluster\IDbCluster', null)
                ->getServer('sqliteMem'),
            'Sqlite memory driver must be available'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IServer',
            $this->dbal
                ->getService('umi\dbal\cluster\IDbCluster', null)
                ->getServer('sqliteFile'),
            'Sqlite memory driver must be available'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IServer',
            $this->dbal
                ->getService('umi\dbal\cluster\IDbCluster', null)
                ->getServer('sqliteEmptyConfig'),
            'Sqlite empty config must create temporary db till end of connection'
        );
    }
}
