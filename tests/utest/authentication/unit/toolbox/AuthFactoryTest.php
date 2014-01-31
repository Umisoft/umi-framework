<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\unit\toolbox;

use umi\authentication\exception\OutOfBoundsException;
use umi\authentication\IAuthenticationFactory;
use umi\authentication\toolbox\factory\AuthenticationFactory;
use utest\authentication\AuthenticationTestCase;

/**
 * Тест аутентификации
 */
class AuthFactoryTest extends AuthenticationTestCase
{
    /**
     * @var IAuthenticationFactory $adapter
     */
    protected $auth;

    public function setUpFixtures()
    {
        $this->auth = new AuthenticationFactory();
        $this->resolveOptionalDependencies($this->auth);
    }

    /**
     * Тест базовых операций
     */
    public function testBasic()
    {
        $this->assertInstanceOf(
            'umi\authentication\Authentication',
            $this->auth->createAuthManager(),
            'Ожидается, что менеджер будет получен.'
        );

        $storage = $this->auth->createStorage(IAuthenticationFactory::STORAGE_SIMPLE);

        $this->assertInstanceOf(
            'umi\authentication\storage\IAuthStorage',
            $storage,
            'Ожидается, что будет возвращено хранилище'
        );

        $adapter = $this->auth->createAdapter(IAuthenticationFactory::ADAPTER_SIMPLE);
        $this->assertInstanceOf(
            'umi\authentication\adapter\IAuthAdapter',
            $adapter,
            'Ожидается, что будет возвращен адаптер'
        );

        $provider = $this->auth->createProvider(IAuthenticationFactory::PROVIDER_SIMPLE);
        $this->assertInstanceOf(
            'umi\authentication\provider\IAuthProvider',
            $provider,
            'Ожидается, что будет возвращен провайдер'
        );
    }

    /**
     * @test получения неверного типа хранилища
     * @expectedException OutOfBoundsException
     */
    public function getInvalidStorage()
    {
        $this->auth->createStorage("INVALID");
    }

    /**
     * @test получения неверного типа адаптера
     * @expectedException OutOfBoundsException
     */
    public function getInvalidAdapter()
    {
        $this->auth->createAdapter("INVALID");
    }

    /**
     * @test получения неверного типа провайдера
     * @expectedException OutOfBoundsException
     */
    public function getInvalidProvider()
    {
        $this->auth->createProvider("INVALID");
    }
}
