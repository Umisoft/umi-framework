<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\unit\provider;

use umi\authentication\provider\HttpProvider;
use umi\http\request\Request;
use utest\authentication\AuthenticationTestCase;

/**
 * Тесты Http провайдера авторизации
 */
class HttpTest extends AuthenticationTestCase
{
    /**
     * @var HttpProvider $provider провайдер
     */
    private $provider;

    public function setUpFixtures()
    {
        $request = new Request();
        $this->resolveOptionalDependencies($request);

        $this->provider = new HttpProvider($request);
        $this->resolveOptionalDependencies($this->provider);
    }

    /**
     * Тест базовых возможностей адаптера
     * @SuppressWarnings(PHPMD)
     */
    public function testBasic()
    {
        $this->assertFalse($this->provider->getCredentials(), 'Ожидается, что данные не установлены');

        $_SERVER['PHP_AUTH_USER'] = 'username';
        $_SERVER['PHP_AUTH_PW'] = 'password';

        $this->assertEquals(
            ['username', 'password'],
            $this->provider->getCredentials(),
            'Ожидается, что данные установлены'
        );
    }
}
