<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata;

use umi\orm\metadata\CollectionDataSource;
use utest\orm\ORMTestCase;

/**
 * Тест источника данных коллекции
 */
class CollectionDataSourceTest extends ORMTestCase
{

    public function testWrongConfig()
    {
        $e = null;
        try {
            new CollectionDataSource([], $this->getDbCluster());
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при создании источника коллекции для метаданных без указания имени таблицы'
        );
    }

    public function testDefaultServers()
    {

        $dataSource = new CollectionDataSource(['sourceName' => 'testTable'], $this->getDbCluster());

        $this->assertEquals(
            'testTable',
            $dataSource->getSourceName(),
            'Ожидается, что вернется установленное имя таблицы'
        );

        $this->assertNull($dataSource->getMasterServerId(), 'Ожидается, что не был выставлен id мастер-сервера');
        $this->assertNull($dataSource->getSlaveServerId(), 'Ожидается, что не был выставлен id слэйв-сервера');

        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IMasterServer',
            $dataSource->getMasterServer(),
            'Ожидается, что ICollectionDataSource::getMasterServer() вернет IMasterServer'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\ISlaveServer',
            $dataSource->getSlaveServer(),
            'Ожидается, что ICollectionDataSource::getSlaveServer() вернет ISlaveServer'
        );

        $this->assertEquals(
            'sqliteMaster',
            $dataSource->getMasterServer()
                ->getId(),
            'Ожидается, что при запросе мастера вернется дефолтный сервер, если он не был выставлен'
        );
        $this->assertEquals(
            'sqliteMaster',
            $dataSource->getSlaveServer()
                ->getId(),
            'Ожидается, что при запросе слейва вернется дефолтный сервер, если он не был выставлен'
        );
        $masterDriver = $this->getDbCluster()->getMaster()->getConnection();
        $this->assertSame(
            $masterDriver,
            $dataSource->getConnection(),
            'Ожидается, что при запросе драйвера вернется драйвер мастера'
        );
    }

    public function testServers()
    {

        $dataSource = new CollectionDataSource([
            'sourceName' => 'testTable',
            'masterServerId' => 'mysqlMaster',
            'slaveServerId' => 'mysqlMaster'
        ], $this->getDbCluster());

        $this->assertEquals(
            'mysqlMaster',
            $dataSource->getMasterServerId(),
            'Ожидается, что ICollectionDataSource::getMasterServerId() вернет выставленный id мастер-сервера'
        );
        $this->assertEquals(
            'mysqlMaster',
            $dataSource->getSlaveServerId(),
            'Ожидается, что ICollectionDataSource::getSlaveServerId() вернет выставленный id слэйв-сервера'
        );

        $this->assertEquals(
            'mysqlMaster',
            $dataSource->getMasterServer()
                ->getId(),
            'Ожидается, что при запросе мастера вернется выставленный сервер, если он был выставлен'
        );
        $this->assertEquals(
            'mysqlMaster',
            $dataSource->getSlaveServer()
                ->getId(),
            'Ожидается, что при запросе слейва вернется выставленный сервер, если он был выставлен'
        );

        $this->assertInstanceOf(
            'Doctrine\DBAL\Driver\PDOMySql\Driver',
            $dataSource->getConnection()->getDriver(),
            'Ожидается, что при запросе драйвера вернется драйвер мастера'
        );

        $this->assertInstanceOf(
            'umi\dbal\builder\ISelectBuilder',
            $dataSource->select(),
            'Ожидается, что ICollectionDataSource::select() вернет ISelectBuilder'
        );
        $this->assertInstanceOf(
            'umi\dbal\builder\IUpdateBuilder',
            $dataSource->update(),
            'Ожидается, что ICollectionDataSource::update() вернет IUpdateBuilder'
        );
        $this->assertInstanceOf(
            'umi\dbal\builder\IDeleteBuilder',
            $dataSource->delete(),
            'Ожидается, что ICollectionDataSource::delete() вернет IDeleteBuilder'
        );
        $this->assertInstanceOf(
            'umi\dbal\builder\IInsertBuilder',
            $dataSource->insert(),
            'Ожидается, что ICollectionDataSource::insert() вернет IInsertBuilder'
        );
    }
}
