<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\unit\toolbox;

use umi\authentication\toolbox\AuthenticationTools;
use umi\authentication\toolbox\IAuthenticationTools;
use utest\TestCase;

/**
 * Тест аутентификации
 */
class AuthenticationToolsTest extends TestCase
{
    /**
     * @var IAuthenticationTools $adapter
     */
    protected $auth;

    public function setUpFixtures()
    {
        $this->auth = new AuthenticationTools();
        $this->resolveOptionalDependencies($this->auth);
    }

    /**
     * Тест базовых операций
     */
    public function testBasic()
    {
        $this->assertInstanceOf(
            'umi\authentication\IAuthenticationFactory',
            $this->auth->getAuthenticationFactory(),
            'Ожидается, что фабрика будет получена.'
        );
    }
}