<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\unit\storage;

use umi\authentication\storage\SessionStorage;
use umi\session\ISession;
use umi\toolkit\Toolkit;
use utest\authentication\AuthenticationTestCase;

/**
 * Тесты storage на основе сессии
 */
class SessionTest extends AuthenticationTestCase
{
    /**
     * @var SessionStorage $storage
     */
    protected $storage;

    /**
     * @var Toolkit $manager менеджер инструментов
     */
    protected $manager;

    public function setUpFixtures()
    {
        $this->storage = new SessionStorage();
        $this->resolveOptionalDependencies($this->storage);
    }

    public function testIdentity()
    {
        $this->assertFalse($this->storage->hasIdentity(), 'Ожидается, что идентификатор не будет существовать');

        $e = null;
        try {
            $this->storage->getIdentity();
        } catch (\Exception $e) {}
        $this->assertInstanceOf('umi\authentication\exception\RuntimeException', $e, 'Ожидается, что идентификатор не был сохранен в сессию');

        $this->assertSame(
            $this->storage,
            $this->storage->setIdentity('identity'),
            'Ожидается, что будет возвращен $this'
        );

        $this->assertEquals('identity', $this->storage->getIdentity(), 'Ожидается, что идентификатор был сохранен');
        $this->assertTrue($this->storage->hasIdentity(), 'Ожидается, что идентификатор будет существовать');

        $this->assertSame($this->storage, $this->storage->clearIdentity(), 'Ожидается, что будет возвращен $this');
        $this->assertFalse($this->storage->hasIdentity(), 'Ожидается, что идентификатор не будет существовать');

    }

    public function testOptions()
    {

        $storage = new SessionStorage();
        $this->resolveOptionalDependencies($storage);
        $storage->setOptions(['namespace' => 'auth']);

        $storage->setIdentity(1);

        /**
         * @var ISession $session
         */
        $session = $this->getTestToolkit()->getService('umi\session\ISession');
        $ns = $session
            ->getNamespace('auth')
            ->toArray();

        $this->assertNotEmpty($ns, 'Ожидается, что пространство имен не пустое.');
    }
}
