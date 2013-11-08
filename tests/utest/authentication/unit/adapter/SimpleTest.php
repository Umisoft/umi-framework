<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\authentication\unit\adapter;

use umi\authentication\adapter\SimpleAdapter;
use umi\authentication\result\IAuthResult;
use utest\TestCase;

/**
 * Тесты simple провайдера авторизиции
 */
class SimpleTest extends TestCase
{

    /**
     * @var SimpleAdapter $adapter
     */
    protected $adapter;

    public function setUpFixtures()
    {
        $this->adapter = new SimpleAdapter();
        $this->resolveOptionalDependencies($this->adapter);
        $this->adapter->allowed = [
            'test_user' => 'password1',
            'user2'     => 'pass2'
        ];
    }

    public function testSuccess()
    {
        $result = $this->adapter->authenticate('user2', 'pass2');

        $this->assertEquals(
            IAuthResult::SUCCESSFUL,
            $result->getStatus(),
            'Ожидается, что авторизация будет пройдена.'
        );
        $this->assertEquals('user2', $result->getIdentity(), 'Ожидается, что идентификатор будет получен.');
    }

    public function testWrongUsername()
    {
        $result = $this->adapter->authenticate('test', 'test');
        $this->assertEquals(
            IAuthResult::WRONG_USERNAME,
            $result->getStatus(),
            'Ожидается, что авторизация не будет пройдена.'
        );
        $this->assertNull($result->getIdentity(), 'Ожидается, что идентификатор не будет получен.');
    }

    public function testWrongPassword()
    {
        $result = $this->adapter->authenticate('user2', 'password wrong');
        $this->assertEquals(
            IAuthResult::WRONG_PASSWORD,
            $result->getStatus(),
            'Ожидается, что авторизация не будет пройдена.'
        );
        $this->assertNull($result->getIdentity(), 'Ожидается, что идентификатор не будет получен.');
    }
}