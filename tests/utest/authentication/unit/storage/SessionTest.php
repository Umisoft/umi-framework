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
use umi\session\toolbox\ISessionTools;
use umi\toolkit\Toolkit;
use utest\TestCase;

/**
 * Тесты storage на основе сессии
 */
class SessionTest extends TestCase
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
        $this->storage = new SessionStorage([], $this->getSessionTools()
            ->getSession());
        $this->resolveOptionalDependencies($this->storage);
    }

    public function testIdentity()
    {
        $this->assertNull($this->storage->getIdentity(), 'Ожидается, что сессия пуста');
        $this->assertFalse($this->storage->hasIdentity(), 'Ожидается, что идентификатор не будет существовать');

        $this->assertSame(
            $this->storage,
            $this->storage->setIdentity('identity'),
            'Ожидается, что будет возвращен $this'
        );

        $this->assertEquals('identity', $this->storage->getIdentity(), 'Ожидается, что идентификатор был сохранен');
        $this->assertTrue($this->storage->hasIdentity(), 'Ожидается, что идентификатор будет существовать');

        $this->assertSame($this->storage, $this->storage->clearIdentity(), 'Ожидается, что будет возвращен $this');
        $this->assertFalse($this->storage->hasIdentity(), 'Ожидается, что идентификатор не будет существовать');
        $this->assertNull($this->storage->getIdentity(), 'Ожидается, что сессия пуста');
    }

    public function testOptions()
    {
        $storage = new SessionStorage([
            'namespace' => 'auth'
        ], $this->getSessionTools()
            ->getSession());

        $this->resolveOptionalDependencies($storage);
        $storage->setIdentity(1);

        $ns = $this->getSessionTools()
            ->getSession()
            ->getNamespace('auth')
            ->toArray();

        $this->assertNotEmpty($ns, 'Ожидается, что пространство имен не пустое.');
    }

    /**
     * @return ISessionTools
     */
    private function getSessionTools()
    {
        return $this->getTestToolkit()
            ->getToolbox(ISessionTools::ALIAS);
    }
}