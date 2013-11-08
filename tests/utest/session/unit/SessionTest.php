<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\unit;

use umi\session\exception\OutOfBoundsException;
use umi\session\exception\RuntimeException;
use umi\session\Session;
use utest\TestCase;

class SessionTest extends TestCase
{
    /**
     * @var Session $factory
     */
    private $sessionService;

    public function setUpFixtures()
    {
        $this->sessionService = new Session();

        $this->resolveOptionalDependencies($this->sessionService);
    }

    public function testBasic()
    {
        $this->assertSame(
            $this->sessionService,
            $this->sessionService->registerNamespace('test'),
            'Ожидается, что будет получен this'
        );

        $this->assertTrue($this->sessionService->hasNamespace('test'));

        $this->assertInstanceOf('umi\session\entity\ns\ISessionNamespace', $this->sessionService->getNamespace('test'));

        $this->assertFalse($this->sessionService->hasNamespace('test 2'));
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function wrongValidatorType()
    {
        $this->sessionService->registerNamespace('name', ['test' => 123]);
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function unregisteredSession()
    {
        $this->sessionService->getNamespace('name');
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function registeredSession()
    {
        $this->sessionService->registerNamespace('my');
        $this->sessionService->registerNamespace('my');
    }

    public function testValidators()
    {
        $_SESSION = [
            'ns' => [
                'meta'   => [],
                'values' => [
                    'my' => 'my'
                ]
            ]
        ];

        $this->sessionService->registerNamespace(
            'ns',
            [
                'mock' => false
            ]
        );

        $this->assertEmpty(
            $this->sessionService->getNamespace('ns')
                ->toArray()
        );
    }
}