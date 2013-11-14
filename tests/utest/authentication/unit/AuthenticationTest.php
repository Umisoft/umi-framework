<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\unit;

use umi\authentication\adapter\SimpleAdapter as SimpleAdapter;
use umi\authentication\Authentication;
use umi\authentication\exception\RuntimeException;
use umi\authentication\provider\SimpleProvider as SimpleProvider;
use umi\authentication\result\IAuthResult;
use umi\authentication\storage\SimpleStorage as SimpleStorage;
use utest\authentication\mock\provider\Wrong;
use utest\TestCase;

/**
 * Тест аутентификации
 */
class AuthenticationTest extends TestCase
{
    /**
     * @var Authentication $adapter
     */
    protected $auth;

    public function setUpFixtures()
    {
        $adapter = new SimpleAdapter([SimpleAdapter::OPTION_ALLOWED_LIST => ['root' => 'root']]);
        $this->resolveOptionalDependencies($adapter);

        $this->auth = new Authentication(
            [],
            $adapter,
            new SimpleStorage()
        );
        $this->resolveOptionalDependencies($this->auth);
    }

    /**
     * Тест базовых операций
     */
    public function testBasic()
    {
        $provider = new SimpleProvider([
            SimpleProvider::OPTION_USERNAME => 'root',
            SimpleProvider::OPTION_PASSWORD => 'root',
        ]);

        $providerEmpty = new SimpleProvider();

        $this->assertFalse($this->auth->isAuthenticated(), 'Ожидается, что мы не авторизованны.');

        $result = $this->auth->authenticate($provider);
        $this->assertInstanceOf('umi\authentication\result\IAuthResult', $result);
        $identity = $result->getIdentity();

        $this->assertTrue($this->auth->isAuthenticated(), 'Ожидается, что что мы авторизованны.');

        $result = $this->auth->authenticate($provider);
        $this->assertInstanceOf('umi\authentication\result\IAuthResult', $result);

        $this->assertTrue(
            $result->isSuccessful(),
            'Ожидается, что авторизация будет успешной.'
        );
        $this->assertEquals(
            $result->getStatus(),
            IAuthResult::ALREADY,
            'Ожидается, что авторизация уже была выполнена.'
        );
        $this->assertEquals(
            $result->getIdentity(),
            $identity,
            'Ожидается, что результат авторизации будет содержать объект авторизации.'
        );

        $this->assertSame(
            $this->auth,
            $this->auth->forget(),
            'Ожидается, что будет возвращен $this'
        );
        $this->assertFalse($this->auth->isAuthenticated(), 'Ожидается, что мы не авторизованны');

        $result = $this->auth->authenticate($providerEmpty);

        $this->assertFalse(
            $result->isSuccessful(),
            'Ожидается, что авторизация не будет успешной'
        );
        $this->assertEquals(
            IAuthResult::WRONG_NO_CREDENTIALS,
            $result->getStatus(),
            'Ожидается, что будет получен статус "нет авторизационных данных".'
        );
        $this->assertEquals(
            null,
            $result->getIdentity(),
            'Ожидается, что ресурс авторизации будет NULL.'
        );
    }

    /**
     * Тест неверного провайдера
     * @expectedException \umi\authentication\exception\RuntimeException
     */
    public function testWrongProvider()
    {
        $provider = new Wrong();
        $this->auth->authenticate($provider);
    }

    public function testHashing()
    {
        $adapter = new SimpleAdapter([
            SimpleAdapter::OPTION_ALLOWED_LIST => ['root' => md5('root' . 'salt')]
        ]);
        $this->resolveOptionalDependencies($adapter);

        $auth = new Authentication(
            [
                Authentication::OPTION_HASH_METHOD => Authentication::HASH_MD5,
                Authentication::OPTION_HASH_SALT   => 'salt',
            ],
            $adapter,
            new SimpleStorage()
        );
        $this->resolveOptionalDependencies($auth);

        $result = $auth->authenticate(
            new SimpleProvider([
                SimpleProvider::OPTION_USERNAME => 'root',
                SimpleProvider::OPTION_PASSWORD => 'root',
            ])
        );

        $this->assertTrue($result->isSuccessful());
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function wrongHashMethod()
    {
        $auth = new Authentication(
            [
                Authentication::OPTION_HASH_METHOD => 'wrong'
            ],
            new SimpleAdapter([]),
            new SimpleStorage()
        );
        $this->resolveOptionalDependencies($auth);

        $auth->authenticate(
            new SimpleProvider([
                SimpleProvider::OPTION_USERNAME => 'root',
                SimpleProvider::OPTION_PASSWORD => 'root',
            ])
        );
    }
}