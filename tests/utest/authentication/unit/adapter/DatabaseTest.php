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
use utest\authentication\AuthenticationTestCase;

/**
 * Тесты simple провайдера авторизиции
 */
class DatabaseTest extends AuthenticationTestCase
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
        $this->adapter = new DatabaseAdapter([
            DatabaseAdapter::OPTION_TABLE => 'users',
            DatabaseAdapter::OPTION_LOGIN_COLUMNS => ['username'],
            DatabaseAdapter::OPTION_PASSWORD_COLUMN => 'password',
        ], $this->getDbCluster());

        $this->resolveOptionalDependencies($this->adapter);

        $this->createTables(
            $this->getDbServer()->getDbDriver()
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
            ->deleteTable('users')
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


        $this->assertEquals(
            [
                'username' => 'root',
                'password' => 'root'
            ],
            $result->getIdentity(),
            'Ожидается, что идентификатор будет записан.'
        );
    }

    public function testWrongAuth()
    {
        $result = $this->adapter->authenticate('root', 'password');
        $this->assertFalse($result->isSuccessful());
        $this->assertEquals(IAuthResult::WRONG_PASSWORD, $result->getStatus());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @param IDbDriver $driver драйвер
     */
    private function createTables(IDbDriver $driver)
    {
        $table = $driver->addTable('users');
        $table->addColumn('username', IColumnScheme::TYPE_VARCHAR);
        $table->addColumn('password', IColumnScheme::TYPE_VARCHAR);
        $table->setPrimaryKey('username');
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
            ->insert('users')
            ->set('username', ':username')
            ->set('password', ':password');
    }
}