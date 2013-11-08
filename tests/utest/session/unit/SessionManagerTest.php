<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\unit;

use umi\http\request\Request;
use umi\session\ISessionManager;
use umi\session\SessionManager;
use utest\session\SessionTestCase;

/**
 * Класс SessionManagerTest
 */
class SessionManagerTest extends SessionTestCase
{

    /**
     * @var ISessionManager $manager менеджер сессии
     */
    protected $manager;

    public function setUpFixtures()
    {
        $request = new Request();
        $this->resolveOptionalDependencies($request);

        $this->manager = new SessionManager($request);
        $this->resolveOptionalDependencies($this->manager);
    }

    public function testBasic()
    {
        $this->assertEquals(
            ISessionManager::STATUS_INACTIVE,
            $this->manager->getStatus(),
            'Ожидается, что сессия не существует'
        );
        $this->assertTrue($this->manager->start(), 'Ожидается, что сессия будет открыта');
        $this->assertFalse($this->manager->start(), 'Ожидается, что сессия второй раз не будет открыта');

        $this->assertEquals(
            ISessionManager::STATUS_ACTIVE,
            $this->manager->getStatus(),
            'Ожидается, что сессия открыта'
        );

        $_SESSION['key'] = 'value';
        $this->manager->write();
        $this->assertEquals(
            ISessionManager::STATUS_CLOSED,
            $this->manager->getStatus(),
            'Ожидается, что сессия закрыта'
        );
        $this->assertArrayHasKey('key', $_SESSION, 'Ожидается, что значение осталось в массиве сессии');

        $this->assertFalse($this->manager->destroy(), 'Ожидается, что сессия уже закрыта');
        $this->manager->start();
        $this->assertTrue($this->manager->destroy(), 'Ожидается, что сессия будет уничтожена');
        $this->assertEmpty($_SESSION, 'Ожидается, что массив сессии пуст');
    }

    public function testMigrate()
    {
        $this->manager->start();
        $sessionId = session_id();
        $this->manager->regenerate();
        $this->assertNotEquals($sessionId, session_id(), 'Ожидается, что идентификатор сессии будет изменен');
    }

    public function testIds()
    {
        $this->assertSame($this->manager, $this->manager->setId(''), 'Ожидается, что будет получен $this');
        $this->assertEmpty($this->manager->getId(), 'Ожидается, что идентификтатор сессии пуст');

        $this->assertSame(
            $this->manager,
            $this->manager->setName('test_session'),
            'Ожидается, что будет получен $this'
        );
        $this->assertEquals('test_session', $this->manager->getName(), 'Ожидается, что идентификтатор сессии пуст');

        $this->manager->setId(md5('time'));
        $this->manager->setName('session');
    }

}