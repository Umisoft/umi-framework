<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\func;

use umi\session\exception\RuntimeException;
use umi\session\ISessionManager;
use utest\session\SessionTestCase;

/**
 * Тестирование сессий
 */
class SessionTest extends SessionTestCase
{

    protected function setUpFixtures()
    {
        $this->getTestToolkit()->getService('umi\session\ISession')->setStorage('null');
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function unregisteredSession()
    {
        $this->getTestToolkit()->getService('umi\session\ISession')
            ->getNamespace('test');
    }

    public function testMockSession()
    {
        $session = $this->getTestToolkit()->getService('umi\session\ISession');
        $session->registerNamespace('test');

        $ns = $session->getNamespace('test');

        $this->assertNull($ns['key'], 'Ожидается, что значения не существует');

        $ns['key'] = 'value';
        $ns = $session->getNamespace('test');

        $this->assertEquals('value', $ns['key'], 'Ожидается, что массив сессии по прежнему доступен');

        $this->getTestToolkit()->getService('umi\session\ISessionManager')->start();

        $this->assertEquals('value', $ns['key'], 'Ожидается, что значение по прежнему существует');
        $this->assertEquals(
            ISessionManager::STATUS_ACTIVE,
            $this->getTestToolkit()->getService('umi\session\ISessionManager')->getStatus()
        );

        $this->getTestToolkit()->getService('umi\session\ISessionManager')->destroy();
        $this->assertEmpty($ns['key'], 'Ожидается, что пространство имен очищено');
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function wrongValidator()
    {
        $session = $this->getTestToolkit()->getService('umi\session\ISession');

        $session->registerNamespace(
            'ns_test',
            [
                'validators' => [
                    'wrong validator' => []
                ]
            ]
        );
    }
}