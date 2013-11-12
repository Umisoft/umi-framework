<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\func;

use umi\authentication\IAuthentication;
use umi\authentication\IAuthenticationFactory;
use umi\authentication\provider\SimpleProvider;
use umi\authentication\toolbox\AuthenticationTools;
use umi\toolkit\factory\TFactory;
use utest\TestCase;

/**
 * Класс AuthTest
 */
class AuthTest extends TestCase
{

    /**
     * @var IAuthentication $auth
     */
    protected $auth;

    public function setUpFixtures()
    {
        /**
         * @var AuthenticationTools $authTools
         */
        $authTools = $this->getTestToolkit()
            ->getToolbox(AuthenticationTools::NAME);
        $this->auth = $authTools->getAuthenticationFactory()
            ->createManager(
            [
                'adapter' => [
                    'type'    => IAuthenticationFactory::ADAPTER_SIMPLE,
                    'options' => [
                        'allowed' => ['user' => 'password']
                    ]
                ]
            ]
        );
    }

    public function tearDownFixtures()
    {
        $this->auth->forget();
    }

    public function testFailureAuth()
    {
        $this->assertFalse($this->auth->isAuthenticated(), 'Ожидается, что мы не авторизованы');

        $provider = new SimpleProvider();
        $provider->username = 'wrong';
        $provider->password = 'wrong';

        $result = $this->auth->authenticate($provider);
        $this->assertFalse($result->isSuccessful(), 'Ожидается, что авторизация не пройдет');
        $this->assertNull($result->getIdentity(), 'Ожидается, что авторизация не пройдет');
    }

    public function testSuccessAuth()
    {
        $this->assertFalse($this->auth->isAuthenticated(), 'Ожидается, что мы не авторизованы');

        $provider = new SimpleProvider();
        $provider->username = 'user';
        $provider->password = 'password';

        $result = $this->auth->authenticate($provider);

        $this->assertInstanceOf('umi\authentication\result\IAuthResult', $result);
        $this->assertTrue($result->isSuccessful(), 'Ожидается, что авторизация успешна');
        $this->assertTrue($this->auth->isAuthenticated(), 'Ожидается, что мы авторизованы');
    }

    public function testAlreadyAuth()
    {
        $this->assertFalse($this->auth->isAuthenticated(), 'Ожидается, что мы не авторизованы');

        $provider = new SimpleProvider();
        $provider->username = 'user';
        $provider->password = 'password';

        $this->auth->authenticate($provider);

        $provider = new SimpleProvider();
        $provider->username = 'wrong';
        $provider->password = 'wrong';

        $result = $this->auth->authenticate($provider);

        $this->assertTrue($result->isSuccessful(), 'Ожидается, что авторизация уже пройдена');
        $this->assertNotNull($result->getIdentity(), 'Ожидается, что авторизация уже пройдена');
    }
}

