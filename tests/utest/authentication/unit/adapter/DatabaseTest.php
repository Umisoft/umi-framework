<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\unit\adapter;

use umi\authentication\adapter\DatabaseAdapter;
use umi\authentication\result\IAuthResult;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\cluster\IConnection;
use umi\dbal\driver\IColumnScheme;
use umi\dbal\driver\IDbDriver;
use utest\TestCase;

/**
 * Тесты simple провайдера авторизиции
 */
class DatabaseTest extends TestCase
{

    /**
     * @var DatabaseAdapter $adapter
     */
    protected $adapter;

    public function setUpFixtures()
    {

        /**
         * @var DatabaseAdapter $adapter
         */
        $this->adapter = new DatabaseAdapter($this->getDbCluster());
        $this->resolveOptionalDependencies($this->adapter);
        $this->createTables(
            $this->getDbServer()
                ->getDbDriver()
        );

        $query = $this->addRow($this->getDBCluster());
        $query->bindString(':username', 'root')
            ->bindString(':password', 'root');
        $query->execute();
    }

    public function tearDownFixtures()
    {
        $this->getDbServer()
            ->getDbDriver()
            ->deleteTable($this->adapter->table)
            ->applyMigrations();
    }

    public function testSuccessAuth()
    {
        $result = $this->adapter->authenticate('root', 'root');

        $this->assertTrue($result->isSuccessful(), 'Ожидается, что авторизация будет пройдена.');
        $this->assertEquals(
            IAuthResult::SUCCESSFUL,
            $result->getStatus(),
            'Ожидается, что авторизация будет пройдена.'
        );

        $user = new \ArrayObject([
            $this->adapter->usernameColumn => 'root',
            $this->adapter->passwordColumn => 'root'
        ], \ArrayObject::ARRAY_AS_PROPS);

        $this->assertEquals(
            $user,
            $result->getIdentity(),
            'Ожидается, что идентификатор будет записан.'
        );
    }

    public function testWrongAuth()
    {
        $result = $this->adapter->authenticate('root', 'password');
        $this->assertFalse($result->isSuccessful());
        $this->assertEquals(IAuthResult::WRONG, $result->getStatus());
        $this->assertNull($result->getIdentity());
    }

    /**
     * Проверка установленного select зависимостей
     * @expectedException \umi\authentication\exception\RuntimeException
     */
    public function testWrongSelect()
    {
        $this->adapter->select = ['wrong', 'select'];
        $this->adapter->authenticate('user', 'password');
    }

    /**
     * @param IDbDriver $driver драйвер
     */
    private function createTables(IDbDriver $driver)
    {
        $table = $driver->addTable($this->adapter->table);
        $table->addColumn($this->adapter->usernameColumn, IColumnScheme::TYPE_VARCHAR);
        $table->addColumn($this->adapter->passwordColumn, IColumnScheme::TYPE_VARCHAR);
        $table->setPrimaryKey($this->adapter->usernameColumn);
        $driver->applyMigrations();
    }

    /**
     * Возвращает запрос добавления пользователя
     * @param IConnection $connection соединение
     * @return IInsertBuilder
     */
    private function addRow(IConnection $connection)
    {
        return $connection
            ->insert($this->adapter->table)
            ->set($this->adapter->usernameColumn, ':username')
            ->set($this->adapter->passwordColumn, ':password');
    }
}