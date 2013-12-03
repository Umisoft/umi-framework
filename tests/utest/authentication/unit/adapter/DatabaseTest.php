<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\unit\adapter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use umi\authentication\adapter\DatabaseAdapter;
use umi\authentication\result\IAuthResult;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\cluster\IConnection;
use utest\authentication\AuthenticationTestCase;

/**
 * Тесты simple провайдера авторизиции
 */
class DatabaseTest extends AuthenticationTestCase
{
    public $connection;

    /**
     * @var DatabaseAdapter $adapter
     */
    protected $adapter;

    public function setUpFixtures()
    {
        $this->connection = $this->getDbCluster();
        /**
         * @var DatabaseAdapter $adapter
         */
        $this->adapter = new DatabaseAdapter([
            DatabaseAdapter::OPTION_TABLE           => 'users',
            DatabaseAdapter::OPTION_LOGIN_COLUMNS   => ['username'],
            DatabaseAdapter::OPTION_PASSWORD_COLUMN => 'password',
        ], $this->connection);

        $this->resolveOptionalDependencies($this->adapter);

        $this->createTables(
            $this->connection->getConnection()
        );

        $query = $this->addRow($this->connection);

        $query
            ->bindString(':username', 'root')
            ->bindString(':password', 'root');

        $query->execute();
    }

    public function tearDownFixtures()
    {
        $this
            ->getDbCluster()
            ->getConnection()
            ->getSchemaManager()
            ->dropTable('users');
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

    public function testWrongPassword()
    {
        $result = $this->adapter->authenticate('root', 'password');
        $this->assertFalse($result->isSuccessful());
        $this->assertEquals(IAuthResult::WRONG_PASSWORD, $result->getStatus());
        $this->assertNull($result->getIdentity());
    }

    public function testWrongUsername()
    {
        $result = $this->adapter->authenticate('foo', 'root');
        $this->assertFalse($result->isSuccessful());
        $this->assertEquals(IAuthResult::WRONG_USERNAME, $result->getStatus());
        $this->assertNull($result->getIdentity());
    }

    /**
     * @param Connection $driver драйвер
     */
    private function createTables(Connection $driver)
    {
        $table = new Table('users');
        $table->addColumn('username', Type::STRING);
        $table->addColumn('password', Type::STRING);
        $table->setPrimaryKey(['username']);
        $driver
            ->getSchemaManager()
            ->createTable($table);
    }

    /**
     * Возвращает запрос добавления пользователя
     * @param IConnection $connection соединение
     *
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
