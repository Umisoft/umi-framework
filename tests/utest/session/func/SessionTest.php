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
use umi\session\toolbox\ISessionTools;
use utest\session\SessionTestCase;

/**
 * Тестирование логгеров
 */
class SessionTest extends SessionTestCase
{

    /**
     * @var ISessionTools $sessionTools
     */
    protected $sessionTools;

    public function setUp()
    {
        /**
         * @var ISessionTools $sessionTools
         */
        $sessionTools = $this->getTestToolkit()
            ->getToolbox(ISessionTools::ALIAS);
        $sessionTools->getSession()
            ->setStorage('null');

        $this->sessionTools = $sessionTools;
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function unregisteredSession()
    {
        $this->sessionTools->getSession()
            ->getNamespace('test');
    }

    public function testMockSession()
    {
        $session = $this->sessionTools->getSession();

        $session->registerNamespace('test');
        $ns = $session->getNamespace('test');

        $this->assertNull($ns['key'], 'Ожидается, что значения не существует');

        $ns['key'] = 'value';
        $ns = $session->getNamespace('test');

        $this->assertEquals('value', $ns['key'], 'Ожидается, что массив сессии по прежнему доступен');

        $this->sessionTools->getManager()
            ->start();

        $this->assertEquals('value', $ns['key'], 'Ожидается, что значение по прежнему существует');
        $this->assertEquals(
            ISessionManager::STATUS_ACTIVE,
            $this->sessionTools->getManager()
                ->getStatus()
        );

        $this->sessionTools->getManager()
            ->destroy();
        $this->assertEmpty($ns['key'], 'Ожидается, что пространство имен очищено');
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function wrongValidator()
    {
        $session = $this->sessionTools->getSession();

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