<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\cluster;

use Doctrine\DBAL\DriverManager;
use umi\dbal\cluster\DbCluster;
use umi\dbal\cluster\IDbCluster;
use umi\dbal\cluster\server\MasterServer;
use umi\dbal\cluster\server\SlaveServer;
use umi\dbal\driver\dialect\MySqlDialect;
use umi\dbal\driver\dialect\SqliteDialect;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\dbal\DbalTestCase;

/**
 * Тестирование компонента "Кластер БД"
 *
 */
class ClusterTest extends DbalTestCase
{

    /**
     * @var IDbCluster $cluster
     */
    protected $cluster;
    /**
     * @var MasterServer;
     */
    protected $mysqlMaster;
    /**
     * @var SlaveServer;
     */
    protected $mysqlSlave;
    /**
     * @var MasterServer;
     */
    protected $sqliteMaster;
    /**
     * @var SlaveServer;
     */
    protected $sqliteSlave;

    protected function setUpFixtures()
    {
        $this->cluster = new DbCluster;

        $mysqlDriver = DriverManager::getConnection(['driver' => 'pdo_mysql']);
        $sqliteDriver = DriverManager::getConnection(['driver' => 'pdo_sqlite']);

        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);

        $this->mysqlMaster = new MasterServer('mysqlMaster', $mysqlDriver, new MySqlDialect(), $queryBuilderFactory);
        $this->mysqlSlave = new SlaveServer('mysqlSlave', $mysqlDriver, new MySqlDialect(), $queryBuilderFactory);
        $this->sqliteMaster = new MasterServer('sqliteMaster', $sqliteDriver, new SqliteDialect(), $queryBuilderFactory);
        $this->sqliteSlave = new SlaveServer('sqliteSlave', $sqliteDriver, new SqliteDialect(), $queryBuilderFactory);
    }

    public function testServers()
    {
        $e = null;
        try {
            $this->cluster->getServer('wrong_server');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при запросе сервера с несуществующим id'
        );

        $this->assertInstanceOf(
            'umi\dbal\cluster\IDbCluster',
            $this->cluster->addServer($this->mysqlMaster),
            'Ожидается, что IDbCluster::addServer() вернет себя'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IServer',
            $this->cluster->getServer('mysqlMaster'),
            'Ожидается, что IDbCluster::getServer() вернет IServer'
        );
    }

    public function testMasterServers()
    {
        $cluster = $this->cluster;

        $e = null;
        try {
            $cluster->getMaster();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            '\umi\dbal\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке запросить мастер-сервер, если он не бул установлен'
        );

        $cluster->addServer($this->mysqlMaster);
        $master = $cluster->getMaster();
        $this->assertInstanceOf('umi\dbal\cluster\server\IMasterServer', $master);
        $this->assertEquals('mysqlMaster', $master->getId());

        $this->assertInstanceOf(
            'umi\dbal\cluster\IDbCluster',
            $cluster->setCurrentMaster($this->sqliteMaster),
            'Ожидается, что IDbCluster::setCurrentMaster() вернет себя'
        );
        $this->assertEquals(
            'sqliteMaster',
            $cluster
                ->getMaster()
                ->getId()
        );
    }

    public function testSlaveServers()
    {
        $cluster = $this->cluster;

        $cluster->addServer($this->mysqlMaster);
        $cluster->addServer($this->mysqlSlave);

        $slave = $cluster->getSlave();
        $this->assertInstanceOf('umi\dbal\cluster\server\ISlaveServer', $slave);
        $this->assertEquals('mysqlSlave', $slave->getId());

        $this->assertInstanceOf(
            'umi\dbal\cluster\IDbCluster',
            $cluster->setCurrentSlave($this->sqliteSlave),
            'Ожидается, что IDbCluster::setCurrentSlave() вернет себя'
        );
        $this->assertEquals(
            'sqliteSlave',
            $cluster
                ->getSlave()
                ->getId()
        );
    }

    public function testConnectionMethods()
    {
        $cluster = $this->cluster;

        $cluster->addServer($this->mysqlMaster);
        $cluster->addServer($this->mysqlSlave);

        $this->assertInstanceOf('Doctrine\DBAL\Connection', $cluster->getConnection());
        $this->assertInstanceOf('umi\dbal\builder\ISelectBuilder', $cluster->select());
        $this->assertInstanceOf('umi\dbal\builder\IUpdateBuilder', $cluster->update('test'));
        $this->assertInstanceOf('umi\dbal\builder\IInsertBuilder', $cluster->insert('test'));
        $this->assertInstanceOf('umi\dbal\builder\IDeleteBuilder', $cluster->delete('test'));
    }

    public function testInternalMethods()
    {
        $this
            ->getDbCluster()
            ->modifyInternal("CREATE TEMPORARY TABLE IF NOT EXISTS `test` (`a` int)");
        $this->assertEquals(
            1,
            $this
                ->getDbCluster()
                ->modifyInternal("INSERT INTO `test` VALUES(1)")
        );
        $this->assertInstanceOf(
            'PDOStatement',
            $this
                ->getDbCluster()
                ->selectInternal("SELECT * FROM `test`")
        );
    }
}
